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
use JWeiland\Reserve\Service\MailService;
use JWeiland\Reserve\Utility\FluidUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Command to send mails using all tx_reserve_domain_model_mail records
 */
class SendMailsCommand extends Command
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

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->emailRepository = $objectManager->get(EmailRepository::class);
        $this->persistenceManager = $objectManager->get(PersistenceManager::class);
    }

    protected function configure()
    {
        $this->setDescription('Send mails using all tx_reserve_domain_model_mail records.');
        $this->setHelp('Send mails using all tx_reserve_domain_model_mail records.');
        $this->addOption('limit', 'm', InputOption::VALUE_OPTIONAL, 'How many mails per execution?', 100);
        $this->addOption(
            'locale',
            'l',
            InputOption::VALUE_OPTIONAL,
            'Locale to be used inside templates and translations. Value that is available inside the Locales class '
            . '(TYPO3\\CMS\\Core\\Localization\\Locales). Example: "default" for english, "de" for german.',
            'default'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $GLOBALS['LANG']->init((string)$input->getOption('locale'));
        $limit = (int)$input->getOption('limit');
        $progressBar = new ProgressBar($output);
        $output->writeln('Send mails...');
        $sentMails = 0;
        while ($sentMails < $limit) {
            if (!$this->sendNextMail()) {
                break;
            }
            $progressBar->advance();
        }
        if ($sentMails === ($limit - 1)) {
            // we have run into the limit so save the current process
            $this->unlockAndUpdateProcessedEmail();
        }

        $progressBar->finish();

        return 0;
    }

    protected function sendNextMail(): bool
    {
        if (!$order = $this->getNextOrder()) {
            // no more mails to send
            return false;
        }

        try {
            GeneralUtility::makeInstance(MailService::class)->sendMailToCustomer(
                $order,
                $this->email->getSubject(),
                FluidUtility::replaceMarkerByRenderedTemplate(
                    '###RESERVATION###',
                    'UpdatedReservation',
                    $this->email->getBody(),
                    ['order' => $order]
                )
            );
        } catch (\Throwable $throwable) {
            $commandData = $this->email->getCommandData();
            $commandData['sendMailExceptions'][$order->getUid()] = json_encode($throwable);
            $this->email->setCommandData($commandData);
        }

        $this->addOrderToProcessedOrders($order);

        return true;
    }

    protected function getNextOrder()
    {
        if(empty($this->orders)) {
            if ($this->email) {
                // all orders of current email are processed now
                $this->unlockAndUpdateProcessedEmail();
                $this->removeProcessedEmail();
            }
            $this->email = $this->emailRepository->findOneUnlocked();
            if ($this->email instanceof Email) {
                // lock current record
                $this->emailRepository->lockEmail($this->email->getUid(), $this->email);
                if (!$this->email->getCommandData()) {
                    $this->email->setCommandData(['processedOrders' => [], 'sendMailExceptions' => []]);
                }
            } else {
                // no more records in db
                return null;
            }
            foreach ($this->email->getPeriods() as $period) {
                foreach ($period->getOrders() as $order) {
                    if (
                        $order->getOrderType() === Order::TYPE_ARCHIVED
                        || in_array($order->getUid(), $this->email->getCommandData(), true)
                    ) {
                        // archived or already processed
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

    protected function removeProcessedEmail()
    {
        $this->persistenceManager->remove($this->email);
        $this->persistenceManager->persistAll();
    }
}
