<?php

declare(strict_types = 1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Facility extends AbstractEntity
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $shortName = '';

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Reserve\Domain\Model\Period>
     */
    protected $periods;

    /**
     * @var string
     */
    protected $confirmationMailSubject = '';

    /**
     * @var string
     */
    protected $fromName = '';

    /**
     * @var string
     */
    protected $fromEmail = '';

    /**
     * @var string
     */
    protected $replyToName = '';

    /**
     * @var string
     */
    protected $replyToEmail = '';

    /**
     * @var string
     */
    protected $confirmationMailHtml = '';

    /**
     * @var string
     */
    protected $reservationMailSubject = '';

    /**
     * @var string
     */
    protected $reservationMailHtml = '';

    /**
     * QR Code settings
     */

    /**
     * @var int
     */
    protected $qrCodeSize = 0;

    /**
     * @var int
     */
    protected $qrCodeLabelSize = 0;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $qrCodeLogo;

    /**
     * @var int
     */
    protected $qrCodeLogoWidth = 0;

    /**
     * @var bool
     */
    protected $cancelable = false;

    /**
     * @var int
     */
    protected $cancelableUntilMinutes = 0;

    public function __construct()
    {
        $this->periods = new ObjectStorage();
        $this->qrCodeLogo = new ObjectStorage();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @param string $shortName
     */
    public function setShortName(string $shortName)
    {
        $this->shortName = $shortName;
    }

    /**
     * @return ObjectStorage
     */
    public function getPeriods(): ObjectStorage
    {
        return $this->periods;
    }

    /**
     * @param ObjectStorage $periods
     */
    public function setPeriods(ObjectStorage $periods)
    {
        $this->periods = $periods;
    }

    /**
     * @return string
     */
    public function getFromName(): string
    {
        return $this->fromName;
    }

    /**
     * @param string $fromName
     */
    public function setFromName(string $fromName)
    {
        $this->fromName = $fromName;
    }

    /**
     * @return string
     */
    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    /**
     * @param string $fromEmail
     */
    public function setFromEmail(string $fromEmail)
    {
        $this->fromEmail = $fromEmail;
    }

    /**
     * @return string
     */
    public function getReplyToName(): string
    {
        return $this->replyToName;
    }

    /**
     * @param string $replyToName
     */
    public function setReplyToName(string $replyToName)
    {
        $this->replyToName = $replyToName;
    }

    /**
     * @return string
     */
    public function getReplyToEmail(): string
    {
        return $this->replyToEmail;
    }

    /**
     * @param string $replyToEmail
     */
    public function setReplyToEmail(string $replyToEmail)
    {
        $this->replyToEmail = $replyToEmail;
    }

    /**
     * @return string
     */
    public function getConfirmationMailSubject(): string
    {
        return $this->confirmationMailSubject;
    }

    /**
     * @param string $confirmationMailSubject
     */
    public function setConfirmationMailSubject(string $confirmationMailSubject)
    {
        $this->confirmationMailSubject = $confirmationMailSubject;
    }

    /**
     * @return string
     */
    public function getConfirmationMailHtml(): string
    {
        return $this->confirmationMailHtml;
    }

    /**
     * @param string $confirmationMailHtml
     */
    public function setConfirmationMailHtml(string $confirmationMailHtml)
    {
        $this->confirmationMailHtml = $confirmationMailHtml;
    }

    /**
     * @return string
     */
    public function getReservationMailSubject(): string
    {
        return $this->reservationMailSubject;
    }

    /**
     * @param string $reservationMailSubject
     */
    public function setReservationMailSubject(string $reservationMailSubject)
    {
        $this->reservationMailSubject = $reservationMailSubject;
    }

    /**
     * @return string
     */
    public function getReservationMailHtml(): string
    {
        return $this->reservationMailHtml;
    }

    /**
     * @param string $reservationMailHtml
     */
    public function setReservationMailHtml(string $reservationMailHtml)
    {
        $this->reservationMailHtml = $reservationMailHtml;
    }

    /**
     * @return int
     */
    public function getQrCodeSize(): int
    {
        return $this->qrCodeSize;
    }

    /**
     * @param int $qrCodeSize
     */
    public function setQrCodeSize(int $qrCodeSize)
    {
        $this->qrCodeSize = $qrCodeSize;
    }

    /**
     * @return int
     */
    public function getQrCodeLabelSize(): int
    {
        return $this->qrCodeLabelSize;
    }

    /**
     * @param int $qrCodeLabelSize
     */
    public function setQrCodeLabelSize(int $qrCodeLabelSize)
    {
        $this->qrCodeLabelSize = $qrCodeLabelSize;
    }

    /**
     * @return ObjectStorage
     */
    public function getQrCodeLogo(): ObjectStorage
    {
        return $this->qrCodeLogo;
    }

    /**
     * @param ObjectStorage $qrCodeLogo
     */
    public function setQrCodeLogo(ObjectStorage $qrCodeLogo)
    {
        $this->qrCodeLogo = $qrCodeLogo;
    }

    /**
     * @return int
     */
    public function getQrCodeLogoWidth(): int
    {
        return $this->qrCodeLogoWidth;
    }

    /**
     * @param int $qrCodeLogoWidth
     */
    public function setQrCodeLogoWidth(int $qrCodeLogoWidth)
    {
        $this->qrCodeLogoWidth = $qrCodeLogoWidth;
    }

    /**
     * @return bool
     */
    public function isCancelable(): bool
    {
        return $this->cancelable;
    }

    /**
     * @return bool
     * @internal fluid getter! In PHP code use isCancelable() instead!
     */
    public function getIsCancelable(): bool
    {
        return $this->isCancelable();
    }

    /**
     * @param bool $cancelable
     */
    public function setCancelable(bool $cancelable)
    {
        $this->cancelable = $cancelable;
    }

    /**
     * @return int
     */
    public function getCancelableUntilMinutes(): int
    {
        return $this->cancelableUntilMinutes;
    }

    /**
     * @param int $cancelableUntilMinutes
     */
    public function setCancelableUntilMinutes(int $cancelableUntilMinutes)
    {
        $this->cancelableUntilMinutes = $cancelableUntilMinutes;
    }
}
