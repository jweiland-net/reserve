<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace JWeiland\Reserve\Command;

use JWeiland\Reserve\Domain\Model\Email;
use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Domain\Repository\EmailRepository;
use JWeiland\Reserve\Utility\MailUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Command to send mails using all tx_reserve_domain_model_mail records
 */
class SendMailCommand extends Command
{
    /**
     * @var EmailRepository
     */
    protected $emailRepository;

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var Email
     */
    protected $email;

    /**
     * @var array|Order[]
     */
    protected $orders = [];

    public function __construct(string $name = null, EmailRepository $emailRepository, PersistenceManager $persistenceManager)
    {
        parent::__construct($name);
        $this->emailRepository = $emailRepository;
        $this->persistenceManager = $persistenceManager;
    }

    protected function configure()
    {
        $this->setDescription('Send mails using all tx_reserve_domain_model_mail records.');
        $this->setHelp('Send mails using all tx_reserve_domain_model_mail records.');
        $this->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'How many mails per execution?', 100);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = (int)$input->getArgument('limit');
        $sentMails = 0;
        while ($sentMails < $limit) {
            if (!$this->sendNextMail()) {
                break;
            }
        }
        if ($sentMails === ($limit - 1)) {
            // we have run into the limit so save this state
            $this->unlockAndUpdateProcessedEmail();
        }
        return 0;
    }

    protected function sendNextMail(): bool
    {
        if (!$order = $this->getNextOrder()) {
            // no more mails to send
            return false;
        }

        try {
            MailUtility::sendMailToCustomer(
                $order,
                $this->email->getSubject(),
                $this->email->getBody() // TODO: str_replace('###RESERVATION###', ...) !!!
            );
            $this->addOrderToProcessedOrders($order);
        } catch (\Throwable $throwable) {
        }

        return true;
    }

    protected function getNextOrder()
    {
        if(empty($this->orders)) {
            if ($this->email) {
                // all orders of current email are processed now
                $this->unlockAndUpdateProcessedEmail();
            }
            $this->email = $this->emailRepository->findOneUnlocked();
            if ($this->email instanceof Email) {
                // lock current record
                $this->emailRepository->lockEmail($this->email->getUid());
                if (!$this->email->getCommandData()) {
                    $this->email->setCommandData(['processedOrders' => []]);
                }
            } else {
                // no more records in db
                return null;
            }
            foreach ($this->email->getPeriods() as $period) {
                foreach ($period->getOrders() as $order) {
                    if (in_array($order->getUid(), $this->email->getCommandData(), true)) {
                        // already processed
                        continue;
                    }
                    $this->orders[] = $order;
                }
            }
        }
        return array_shift($this->orders);
    }

    protected function addOrderToProcessedOrders(Order $order)
    {
        $commandData = $this->email->getCommandData();
        $commandData['processedOrders'][] = $order->getUid();
        $this->email->setCommandData($commandData);
    }

    protected function unlockAndUpdateProcessedEmail()
    {
        $this->email->setLocked(false);
        $this->persistenceManager->add($this->email);
        $this->persistenceManager->persistAll();
    }
}
