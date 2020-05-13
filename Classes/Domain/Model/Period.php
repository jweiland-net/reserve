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

class Period extends AbstractEntity
{
    /**
     * @var \JWeiland\Reserve\Domain\Model\Facility
     */
    protected $facility;

    /**
     * @var \DateTime
     */
    protected $bookingBegin;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var \DateTime
     */
    protected $begin;

    /**
     * @var \DateTime
     */
    protected $end;

    protected $maxParticipants = 0;

    protected $maxParticipantsPerOrder = 0;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Reserve\Domain\Model\Order>
     */
    protected $orders;

    public function __construct()
    {
        $this->orders = new ObjectStorage();
    }

    /**
     * @return Facility
     */
    public function getFacility(): Facility
    {
        return $this->facility;
    }

    /**
     * @param Facility $facility
     */
    public function setFacility(Facility $facility)
    {
        $this->facility = $facility;
    }

    /**
     * @return \DateTime
     */
    public function getBookingBegin(): \DateTime
    {
        return $this->bookingBegin;
    }

    /**
     * @param \DateTime $bookingBegin
     */
    public function setBookingBegin(\DateTime $bookingBegin)
    {
        $this->bookingBegin = $bookingBegin;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getBegin(): \DateTime
    {
        return $this->begin;
    }

    /**
     * @param \DateTime $begin
     */
    public function setBegin(\DateTime $begin)
    {
        $this->begin = $begin;
    }

    /**
     * @return \DateTime
     */
    public function getEnd(): \DateTime
    {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     */
    public function setEnd(\DateTime $end)
    {
        $this->end = $end;
    }

    /**
     * @return int
     */
    public function getMaxParticipants(): int
    {
        return $this->maxParticipants;
    }

    /**
     * @param int $maxParticipants
     */
    public function setMaxParticipants(int $maxParticipants)
    {
        $this->maxParticipants = $maxParticipants;
    }

    /**
     * @return int
     */
    public function getMaxParticipantsPerOrder(): int
    {
        return $this->maxParticipantsPerOrder;
    }

    /**
     * @param int $maxParticipantsPerOrder
     */
    public function setMaxParticipantsPerOrder(int $maxParticipantsPerOrder)
    {
        $this->maxParticipantsPerOrder = $maxParticipantsPerOrder;
    }

    /**
     * @return ObjectStorage
     */
    public function getOrders(): ObjectStorage
    {
        return $this->orders;
    }

    /**
     * @param ObjectStorage $orders
     */
    public function setOrders(ObjectStorage $orders)
    {
        $this->orders = $orders;
    }

    public function isBookable(): bool
    {
        return $this->bookingBegin->getTimestamp() >= time();
    }
}
