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
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service to send mails
 */
class MailService implements SingletonInterface
{
    public function sendMailToCustomer(Order $order, string $subject, string $bodyHtml, \Closure $postProcess = null): bool
    {
        return $this->sendMail(
            $subject,
            $bodyHtml,
            $order->getEmail(),
            $order->getBookedPeriod()->getFacility()->getFromEmail(),
            $order->getBookedPeriod()->getFacility()->getFromName(),
            $order->getBookedPeriod()->getFacility()->getReplyToEmail(),
            $order->getBookedPeriod()->getFacility()->getReplyToName(),
            $postProcess,
            ['order' => $order]
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
        array $postProcessData = []
    ): bool {
        /** @var MailMessage $mail */
        $mail = GeneralUtility::makeInstance(MailMessage::class);
        $mail
            ->setSubject($subject)
            ->setTo([$to]);
        if ($from) {
            $mail->setFrom([$from => $fromName]);
        }
        if ($replyTo) {
            $mail->setReplyTo([$replyTo => $replyToName]);
        }

        if (method_exists($mail, 'addPart')) {
            $isSymfonyEmail = false;
            // TYPO3 < 10 (Swift_Message)
            $mail->setBody($bodyHtml, 'text/html');
        } else {
            $isSymfonyEmail = true;
            // TYPO3 >= 10 (Symfony Mail)
            $mail->html($bodyHtml);
        }

        // closure hook to add your own stuff to the $mail
        // use $isSymfonyEmail to check if current TYPO3 is running >= v10 with the new symfony email!
        if ($postProcess) {
            $postProcess($postProcessData, $subject, $bodyHtml, $mail, $isSymfonyEmail);
        }

        return (bool)$mail->send();
    }
}
