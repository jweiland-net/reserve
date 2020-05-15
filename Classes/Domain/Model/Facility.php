<?php

declare(strict_types = 1);

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

namespace JWeiland\Reserve\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Facility extends AbstractEntity
{
    protected $name = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Reserve\Domain\Model\Period>
     */
    protected $periods;

    protected $confirmationMailSubject = '';

    protected $replyToName = '';

    protected $replyToEmail = '';

    protected $confirmationMailHtml = '';

    protected $reservationMailSubject = '';

    protected $reservationMailHtml = '';

    public function __construct()
    {
        $this->periods = new ObjectStorage();
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
}
