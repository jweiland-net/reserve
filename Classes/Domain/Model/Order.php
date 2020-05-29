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

namespace JWeiland\Reserve\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Reservation
 */
class Order extends AbstractEntity
{
    const TYPE_DEFAULT = 0;
    const TYPE_ARCHIVED = 1;

    /**
     * @var \JWeiland\Reserve\Domain\Model\Period
     */
    protected $bookedPeriod;

    /**
     * @var string
     */
    protected $activationCode = '';

    /**
     * @var bool
     */
    protected $activated = false;

    /**
     * @var string
     */
    protected $email = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Reserve\Domain\Model\Reservation>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $reservations;

    /**
     * @var int use Order::TYPE_ constants to compare or set this value!
     */
    protected $orderType = self::TYPE_DEFAULT;

    public function __construct()
    {
        $this->reservations = new ObjectStorage();
    }

    /**
     * @return Period
     */
    public function getBookedPeriod(): Period
    {
        return $this->bookedPeriod;
    }

    /**
     * @param Period $bookedPeriod
     */
    public function setBookedPeriod(Period $bookedPeriod)
    {
        $this->bookedPeriod = $bookedPeriod;
    }

    /**
     * @return string
     */
    public function getActivationCode(): string
    {
        return $this->activationCode;
    }

    /**
     * @param string $activationCode
     */
    public function setActivationCode(string $activationCode)
    {
        $this->activationCode = $activationCode;
    }

    /**
     * @return bool
     */
    public function isActivated(): bool
    {
        return $this->activated;
    }

    /**
     * @param bool $activated
     */
    public function setActivated(bool $activated)
    {
        $this->activated = $activated;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return ObjectStorage|Reservation[]
     */
    public function getReservations(): ObjectStorage
    {
        return $this->reservations;
    }

    /**
     * @param ObjectStorage $reservations
     */
    public function setReservations(ObjectStorage $reservations)
    {
        $this->reservations = $reservations;
    }

    /**
     * @return int use Order::TYPE_ constants to compare or set this value!
     */
    public function getOrderType(): int
    {
        return $this->orderType;
    }

    /**
     * @param int $orderType use Order::TYPE_ constants to compare or set this value!
     */
    public function setOrderType(int $orderType)
    {
        $this->orderType = $orderType;
    }
}
