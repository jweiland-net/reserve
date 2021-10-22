<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
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

    /**
     * @var array|string[]
     */
    protected $receivers = [];

    /**
     * @var int
     */
    protected $currentReceiverKey = 0;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->emailRepository = $objectManager->get(EmailRepository::class);
        $this->persistenceManager = $objectManager->get(PersistenceManager::class);
    }

    protected function configure(): void
    {
        $this->setDescription('Send mails using all tx_reserve_domain_model_mail records.');
        $this->setHelp('Send mails using all tx_reserve_domain_model_mail records.');
        $this->addOption('mailLimit', 'm', InputOption::VALUE_OPTIONAL, 'How many mails per execution?', 100);
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
        $mailLimit = (int)$input->getOption('mailLimit');
        $progressBar = new ProgressBar($output);
        $output->writeln('Send mails...');
        $sentMails = 0;
        while ($sentMails < $mailLimit) {
            if (!$this->sendNextMail()) {
                break;
            }
            $sentMails++;
            $progressBar->advance();
        }
        if ($sentMails === ($mailLimit - 1)) {
            // we have run into the mailLimit so save the current process
            $this->unlockAndUpdateProcessedEmail();
        }

        $progressBar->finish();

        return 0;
    }

    protected function sendNextMail(): bool
    {
        if (!$receiver = $this->getNextReceiver()) {
            // no more mails to send
            return false;
        }

        try {
            if ($this->email->getReceiverType() === Email::RECEIVER_TYPE_PERIODS) {
                // attach the associated order because we know that an order exists
                $order = $this->orders[$this->currentReceiverKey];
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
            } else {
                GeneralUtility::makeInstance(MailService::class)->sendMail(
                    $this->email->getSubject(),
                    $this->email->getBody(),
                    $receiver,
                    $this->email->getFromEmail(),
                    $this->email->getFromName(),
                    $this->email->getReplyToEmail(),
                    $this->email->getReplyToName()
                );
            }
        } catch (\Throwable $throwable) {
            $commandData = $this->email->getCommandData();
            $commandData['sendMailExceptions'][$this->currentReceiverKey] = json_encode($throwable);
            $this->email->setCommandData($commandData);
        }

        $this->addCurrentReceiverToProcessedReceivers();

        return true;
    }

    protected function getNextReceiver(): string
    {
        if (empty($this->receivers)) {
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
                    $this->email->setCommandData(['processedReceiversByKey' => [], 'sendMailExceptions' => []]);
                }
            } else {
                // no more records in db
                return '';
            }
            $this->receivers = $this->email->getReceivers($this->orders);
        }
        $this->currentReceiverKey = key($this->receivers);
        return (string)array_shift($this->receivers);
    }

    protected function addCurrentReceiverToProcessedReceivers(): void
    {
        $commandData = $this->email->getCommandData();
        $commandData['processedReceiversByKey'][] = $this->currentReceiverKey;
        $this->email->setCommandData($commandData);
    }

    protected function unlockAndUpdateProcessedEmail(): void
    {
        $this->email->setLocked(false);
        $this->persistenceManager->add($this->email);
        $this->persistenceManager->persistAll();
    }

    protected function removeProcessedEmail(): void
    {
        $this->persistenceManager->remove($this->email);
        $this->persistenceManager->persistAll();
    }
}
