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

namespace JWeiland\Reserve\Utility;

use JWeiland\Reserve\Domain\Model\Order;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Utility to send mails
 */
class MailUtility
{
    public static function sendMailToCustomer(Order $order, string $subject, string $bodyHtml): bool
    {
        /** @var MailMessage $mail */
        $mail = GeneralUtility::makeInstance(MailMessage::class);
        $mail
            ->setSubject($subject)
            ->setTo([$order->getEmail()]);
        if ($order->getBookedPeriod()->getFacility()->getReplyToEmail()) {
            $mail->setReplyTo([$order->getBookedPeriod()->getFacility()->getReplyToEmail() => $order->getBookedPeriod()->getFacility()->getReplyToName()]);
        }
        if (method_exists($mail, 'addPart')) {
            // TYPO3 < 10 (Swift_Message)
            $mail->setBody($bodyHtml, 'text/html');
        } else {
            // TYPO3 >= 10 (Symfony Mail)
            $mail->html($bodyHtml);
        }
        return (bool)$mail->send();
    }
}
