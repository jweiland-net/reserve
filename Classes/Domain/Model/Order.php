<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
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
    protected $firstName = '';

    /**
     * @var string
     */
    protected $lastName = '';

    /**
     * @var string
     */
    protected $email = '';

    /**
     * @var string
     */
    protected $phone = '';

    /**
     * @var string
     */
    protected $address = '';

    /**
     * @var string
     */
    protected $zip = '';

    /**
     * @var string
     */
    protected $city = '';

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Transient
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Reserve\Domain\Model\Participant>
     */
    protected $participants = [];

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Reserve\Domain\Model\Reservation>
     */
    protected $reservations;

    /**
     * @var int use Order::TYPE_ constants to compare or set this value!
     */
    protected $orderType = self::TYPE_DEFAULT;

    public function __construct()
    {
        $this->reservations = new ObjectStorage();
        $this->participants = new ObjectStorage();
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
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
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
     * @param string $phone
     */
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $zip
     */
    public function setZip(string $zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param ObjectStorage|Participant[] $participants
     */
    public function setParticipants(ObjectStorage $participants)
    {
        foreach ($participants as $participant) {
            if ($participant->getFirstName() || $participant->getLastName()) {
                $this->participants->attach($participant);
            }
        }
    }

    /**
     * @return ObjectStorage|Participant[]
     */
    public function getParticipants(): ObjectStorage
    {
        return $this->participants;
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

    public function isCancelable(): bool
    {
        $cancelable = false;
        if ($this->activated === false) {
            // non activated orders can be cancelled in any case
            $cancelable = true;
        } elseif ($this->getBookedPeriod()->getFacility()->isCancelable()) {
            // if the facility allows cancellations then check if it's not to late to cancel
            return new \DateTime() <= $this->getCancelableUntil();
        }
        return $cancelable;
    }

    /**
     * @return \DateTime|null DateTime or null if facility does not allow to cancel reservations
     */
    public function getCancelableUntil()
    {
        $cancelableUntil = null;
        if ($this->getBookedPeriod()->getFacility()->isCancelable()) {
            $cancelableUntil = clone $this->getBookedPeriod()->getBeginDateAndTime();
            $cancelableUntil->modify('-' . $this->getBookedPeriod()->getFacility()->getCancelableUntilMinutes() . 'minutes');
        }
        return $cancelableUntil;
    }
}
