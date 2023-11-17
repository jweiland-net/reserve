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
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Reservation
 */
class Order extends AbstractEntity
{
    protected Period $bookedPeriod;

    protected string $activationCode = '';

    protected bool $activated = false;

    protected string $firstName = '';

    protected string $lastName = '';

    protected string $email = '';

    protected string $phone = '';

    protected string $address = '';

    protected string $zip = '';

    protected string $city = '';

    protected string $organization = '';

    protected string $remarks = '';

    /**
     * @var ObjectStorage<Participant>
     *
     * @Extbase\ORM\Transient
     */
    protected ObjectStorage $participants;

    /**
     * @var ObjectStorage<Reservation>
     *
     * @Extbase\ORM\Cascade("remove")
     */
    protected ObjectStorage $reservations;

    public function __construct()
    {
        $this->participants = new ObjectStorage();
        $this->reservations = new ObjectStorage();
    }

    /**
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject(): void
    {
        $this->participants = $this->participants ?? new ObjectStorage();
        $this->reservations = $this->reservations ?? new ObjectStorage();
    }

    public function getBookedPeriod(): Period
    {
        return $this->bookedPeriod;
    }

    public function setBookedPeriod(Period $bookedPeriod): void
    {
        $this->bookedPeriod = $bookedPeriod;
    }

    public function getActivationCode(): string
    {
        return $this->activationCode;
    }

    public function setActivationCode(string $activationCode): void
    {
        $this->activationCode = $activationCode;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): void
    {
        $this->activated = $activated;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getOrganization(): string
    {
        return $this->organization;
    }

    public function setOrganization(string $organization): void
    {
        $this->organization = $organization;
    }

    public function getRemarks(): string
    {
        return $this->remarks;
    }

    public function setRemarks(string $remarks): void
    {
        $this->remarks = $remarks;
    }

    /**
     * @return ObjectStorage|Participant[]
     */
    public function getParticipants(): ObjectStorage
    {
        return $this->participants;
    }

    /**
     * @param ObjectStorage|Participant[] $participants
     */
    public function setParticipants(ObjectStorage $participants): void
    {
        foreach ($participants as $participant) {
            if ($participant->getFirstName() || $participant->getLastName()) {
                $this->participants->attach($participant);
            }
        }
    }

    public function addParticipant(Participant $participant): void
    {
        if ($participant->getFirstName() || $participant->getLastName()) {
            $this->participants->attach($participant);
        }
    }

    public function removeParticipant(Participant $participant): void
    {
        $this->participants->detach($participant);
    }

    /**
     * @return ObjectStorage|Reservation[]
     */
    public function getReservations(): ObjectStorage
    {
        return $this->reservations;
    }

    public function setReservations(ObjectStorage $reservations): void
    {
        $this->reservations = $reservations;
    }

    public function addReservation(Reservation $reservation): void
    {
        $this->reservations->attach($reservation);
    }

    public function removeReservation(Reservation $reservation): void
    {
        $this->reservations->detach($reservation);
    }

    public function isCancelable(): bool
    {
        $cancelable = false;
        if ($this->activated === false) {
            // non activated orders can be cancelled in any case
            $cancelable = true;
        } elseif ($this->getBookedPeriod()->getFacility()->isCancelable()) {
            // if the facility allows cancellations then check if it's not too late to cancel
            $cancelable = new \DateTime() <= $this->getCancelableUntil();
        }

        return $cancelable;
    }

    /**
     * @return \DateTime|null DateTime or null if facility does not allow canceling reservations
     */
    public function getCancelableUntil(): ?\DateTime
    {
        $cancelableUntil = null;
        if ($this->getBookedPeriod()->getFacility()->isCancelable()) {
            $cancelableUntil = clone $this->getBookedPeriod()->getBeginDateAndTime();
            $cancelableUntil->modify('-' . $this->getBookedPeriod()->getFacility()->getCancelableUntilMinutes() . 'minutes');
        }

        return $cancelableUntil;
    }

    /**
     * Returns true, if order with current number of participants can be booked.
     * Hint: This method does not check, if a further participant can be added!
     */
    public function canBeBooked(): bool
    {
        $numberOfParticipants = $this->getParticipants()->count();

        return $numberOfParticipants <= $this->getBookedPeriod()->getMaxParticipantsPerOrder();
    }

    public function shouldBlockFurtherOrdersForFacility(): bool
    {
        return true;
    }
}
