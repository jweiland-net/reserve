<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
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
     * @var ObjectStorage<Period>
     *
     * @Extbase\ORM\Lazy
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
     * @var ObjectStorage<FileReference>
     *
     * @Extbase\ORM\Lazy
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
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject(): void
    {
        $this->periods = $this->periods ?? new ObjectStorage();
        $this->qrCodeLogo = $this->qrCodeLogo ?? new ObjectStorage();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
    }

    /**
     * @return ObjectStorage|Period[]
     */
    public function getPeriods(): ObjectStorage
    {
        return $this->periods;
    }

    public function setPeriods(ObjectStorage $periods): void
    {
        $this->periods = $periods;
    }

    public function getFromName(): string
    {
        return $this->fromName;
    }

    public function setFromName(string $fromName): void
    {
        $this->fromName = $fromName;
    }

    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    public function setFromEmail(string $fromEmail): void
    {
        $this->fromEmail = $fromEmail;
    }

    public function getReplyToName(): string
    {
        return $this->replyToName;
    }

    public function setReplyToName(string $replyToName): void
    {
        $this->replyToName = $replyToName;
    }

    public function getReplyToEmail(): string
    {
        return $this->replyToEmail;
    }

    public function setReplyToEmail(string $replyToEmail): void
    {
        $this->replyToEmail = $replyToEmail;
    }

    public function getConfirmationMailSubject(): string
    {
        return $this->confirmationMailSubject;
    }

    public function setConfirmationMailSubject(string $confirmationMailSubject): void
    {
        $this->confirmationMailSubject = $confirmationMailSubject;
    }

    public function getConfirmationMailHtml(): string
    {
        return $this->confirmationMailHtml;
    }

    public function setConfirmationMailHtml(string $confirmationMailHtml): void
    {
        $this->confirmationMailHtml = $confirmationMailHtml;
    }

    public function getReservationMailSubject(): string
    {
        return $this->reservationMailSubject;
    }

    public function setReservationMailSubject(string $reservationMailSubject): void
    {
        $this->reservationMailSubject = $reservationMailSubject;
    }

    public function getReservationMailHtml(): string
    {
        return $this->reservationMailHtml;
    }

    public function setReservationMailHtml(string $reservationMailHtml): void
    {
        $this->reservationMailHtml = $reservationMailHtml;
    }

    public function getQrCodeSize(): int
    {
        return $this->qrCodeSize;
    }

    public function setQrCodeSize(int $qrCodeSize): void
    {
        $this->qrCodeSize = $qrCodeSize;
    }

    public function getQrCodeLabelSize(): int
    {
        return $this->qrCodeLabelSize;
    }

    public function setQrCodeLabelSize(int $qrCodeLabelSize): void
    {
        $this->qrCodeLabelSize = $qrCodeLabelSize;
    }

    /**
     * @return ObjectStorage|FileReference[]
     */
    public function getQrCodeLogo(): ObjectStorage
    {
        return $this->qrCodeLogo;
    }

    public function setQrCodeLogo(ObjectStorage $qrCodeLogo): void
    {
        $this->qrCodeLogo = $qrCodeLogo;
    }

    public function getQrCodeLogoWidth(): int
    {
        return $this->qrCodeLogoWidth;
    }

    public function setQrCodeLogoWidth(int $qrCodeLogoWidth): void
    {
        $this->qrCodeLogoWidth = $qrCodeLogoWidth;
    }

    public function isCancelable(): bool
    {
        return $this->cancelable;
    }

    /**
     * @internal fluid getter! In PHP code use isCancelable() instead!
     */
    public function getIsCancelable(): bool
    {
        return $this->isCancelable();
    }

    public function setCancelable(bool $cancelable): void
    {
        $this->cancelable = $cancelable;
    }

    public function getCancelableUntilMinutes(): int
    {
        return $this->cancelableUntilMinutes;
    }

    public function setCancelableUntilMinutes(int $cancelableUntilMinutes): void
    {
        $this->cancelableUntilMinutes = $cancelableUntilMinutes;
    }
}
