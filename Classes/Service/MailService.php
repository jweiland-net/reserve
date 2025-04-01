<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Service;

use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Event\SendEmailEvent;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service to send mails
 */
class MailService implements SingletonInterface
{
    protected EventDispatcher $eventDispatcher;

    public function __construct(
        EventDispatcher $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function sendMailToCustomer(
        Order $order,
        string $subject,
        string $bodyHtml,
        \Closure $postProcess = null,
    ): bool {
        return $this->sendMail(
            $subject,
            $bodyHtml,
            $order->getEmail(),
            $order->getBookedPeriod()->getFacility()->getFromEmail(),
            $order->getBookedPeriod()->getFacility()->getFromName(),
            $order->getBookedPeriod()->getFacility()->getReplyToEmail(),
            $order->getBookedPeriod()->getFacility()->getReplyToName(),
            $postProcess,
            ['order' => $order],
        );
    }

    public function sendMail(
        string $subject,
        string $bodyHtml,
        string $to,
        string $from = '',
        string $fromName = '',
        string $replyTo = '',
        string $replyToName = '',
        \Closure $postProcess = null,
        array $postProcessData = [],
    ): bool {
        $mail = $this->getMailMessage();
        $mail
            ->setSubject($subject)
            ->setTo([$to]);

        if ($from) {
            $mail->setFrom([$from => $fromName]);
        }

        if ($replyTo) {
            $mail->setReplyTo([$replyTo => $replyToName]);
        }

        $mail->html($bodyHtml);

        // closure hook to add your own stuff to the $mail
        if ($postProcess) {
            $postProcess($postProcessData, $subject, $bodyHtml, $mail);
        }

        /** @var SendEmailEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new SendEmailEvent($mail),
        );
        $mail = $event->getMailMessage();

        return $mail->send();
    }

    protected function getMailMessage(): MailMessage
    {
        return GeneralUtility::makeInstance(MailMessage::class);
    }
}
