<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Event;

use TYPO3\CMS\Core\Mail\MailMessage;

final class SendCancellationEmailEvent
{
    protected MailMessage $mailMessage;

    public function __construct(MailMessage $mailMessage)
    {
        $this->mailMessage = $mailMessage;
    }

    public function getMailMessage(): MailMessage
    {
        return $this->mailMessage;
    }
}
