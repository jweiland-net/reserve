<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Domain\Model;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Period extends AbstractEntity
{
    protected Facility $facility;

    protected \DateTime $bookingBegin;

    protected \DateTime $bookingEnd;

    protected \DateTime $date;

    /**
     * 00:00 is still a valid value, but will be stored in DB with "0".
     * DataHandler will return NULL on empty values.
     * So we have to add NULL as possible return value here.
     */
    protected ?\DateTime $begin;

    /**
     * 00:00 is still a valid value, but will be stored in DB with "0".
     * DataHandler will return NULL on empty values.
     * So we have to add NULL as possible return value here.
     */
    protected ?\DateTime $end;

    protected int $maxParticipants = 0;

    protected int $maxParticipantsPerOrder = 0;

    /**
     * @var ObjectStorage<Order>
     *
     * @Extbase\ORM\Lazy
     */
    protected ObjectStorage $orders;

    /**
     * @internal Cache property for query results
     */
    protected array $cache = [];

    public function __construct()
    {
        $this->orders = new ObjectStorage();
    }

    /**
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject(): void
    {
        $this->orders = $this->orders ?? new ObjectStorage();
    }

    public function getFacility(): Facility
    {
        return $this->facility;
    }

    public function setFacility(Facility $facility): void
    {
        $this->facility = $facility;
    }

    public function getBookingBegin(): \DateTime
    {
        return $this->bookingBegin;
    }

    public function setBookingBegin(\DateTime $bookingBegin): void
    {
        $this->bookingBegin = $bookingBegin;
    }

    /**
     * @return \DateTime|null null if no booking end was set!
     */
    public function getBookingEnd(): ?\DateTime
    {
        return $this->bookingEnd;
    }

    public function setBookingEnd(\DateTime $bookingEnd): void
    {
        $this->bookingEnd = $bookingEnd;
    }

    public function getDate(): \DateTime
    {
        if (property_exists($this->date, 'timezone_type') && $this->date->timezone_type !== 3) {
            $this->date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        }

        return $this->date;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getBegin(): ?\DateTime
    {
        if ($this->begin instanceof \DateTime && $this->begin->getTimezone()->getName() !== 'UTC') {
            $this->begin->setTimezone(new \DateTimeZone('UTC'));
        }

        return $this->begin;
    }

    public function setBegin(\DateTime $begin): void
    {
        $this->begin = $begin;
    }

    public function getEnd(): ?\DateTime
    {
        if ($this->end instanceof \DateTime && $this->end->getTimezone()->getName() !== 'UTC') {
            $this->end->setTimezone(new \DateTimeZone('UTC'));
        }

        return $this->end;
    }

    public function setEnd(\DateTime $end): void
    {
        $this->end = $end;
    }

    public function getMaxParticipants(): int
    {
        return $this->maxParticipants;
    }

    public function setMaxParticipants(int $maxParticipants): void
    {
        $this->maxParticipants = MathUtility::forceIntegerInRange(
            $maxParticipants,
            0
        );
    }

    public function getRemainingParticipants(): int
    {
        return MathUtility::forceIntegerInRange(
            $this->maxParticipants - $this->countReservations(),
            0
        );
    }

    public function getMaxParticipantsPerOrder(): int
    {
        return min($this->maxParticipantsPerOrder, $this->getRemainingParticipants());
    }

    /**
     * Allows to iterate over the number of allowed further participants.
     */
    public function getMaxFurtherParticipantsPerOrderIterable(): array
    {
        // less 1 because the owner of the ticket is a participant too
        $available = $this->getMaxParticipantsPerOrder() - 1;
        if ($available < 1) {
            return [];
        }

        return range(1, $available);
    }

    public function setMaxParticipantsPerOrder(int $maxParticipantsPerOrder): void
    {
        $this->maxParticipantsPerOrder = MathUtility::forceIntegerInRange(
            $maxParticipantsPerOrder,
            0
        );
    }

    /**
     * @return ObjectStorage|Order[]
     */
    public function getOrders(): ObjectStorage
    {
        return $this->orders;
    }

    public function setOrders(ObjectStorage $orders): void
    {
        $this->orders = $orders;
    }

    public function addOrder(Order $order): void
    {
        $this->orders->attach($order);
    }

    public function removeOrder(Order $order): void
    {
        $this->orders->detach($order);
    }

    public function isBookable(): bool
    {
        $bookable = true;
        if (!$this->isBookingBeginReached()) {
            // bookings have not started yet
            $bookable = false;
        }

        if ($this->isBookingTimeOver()) {
            // booking time is over
            $bookable = false;
        }

        return $bookable;
    }

    public function isBookingBeginReached(): bool
    {
        return time() >= $this->bookingBegin->getTimestamp();
    }

    public function isBookingTimeOver(): bool
    {
        return $this->bookingEnd && $this->bookingEnd->getTimestamp() <= time();
    }

    /**
     * @internal fluid only!
     */
    public function getIsBookingBeginReached(): bool
    {
        return $this->isBookingBeginReached();
    }

    /**
     * @internal fluid only!
     */
    public function getIsBookingTimeOver(): bool
    {
        return $this->isBookingTimeOver();
    }

    /**
     * @internal fluid only! Use isBookable() instead!
     */
    public function getIsBookable(): bool
    {
        return $this->isBookable();
    }

    public function getReservations(bool $activeOnly = false): array
    {
        $reservations = [];

        foreach ($this->getOrders() as $order) {
            if (!$activeOnly || $order->isActivated()) {
                foreach ($order->getReservations() as $reservation) {
                    $reservations[] = $reservation;
                }
            }
        }

        return $reservations;
    }

    public function countReservations(bool $activeOnly = false): int
    {
        // ToDo: SF: DB queries in models?! Should be reworked in future
        $cacheIdentifier = 'countReservations' . ($activeOnly ? 'ActiveOnly' : '');
        if (!array_key_exists($cacheIdentifier, $this->cache)) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable('tx_reserve_domain_model_reservation');
            $queryBuilder
                ->count('r.uid')
                ->from('tx_reserve_domain_model_order', 'o')
                ->leftJoin(
                    'o',
                    'tx_reserve_domain_model_reservation',
                    'r',
                    'r.customer_order = o.uid'
                )
                ->where($queryBuilder->expr()->eq(
                    'o.booked_period',
                    $queryBuilder->createNamedParameter($this->getUid())
                ));

            if ($activeOnly) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('o.activated', 1));
            }

            $this->cache[$cacheIdentifier] = (int)$queryBuilder->executeQuery()->fetchOne();
        }

        return $this->cache[$cacheIdentifier];
    }

    public function getActiveReservations(): array
    {
        return $this->getReservations(true);
    }

    public function getCountActiveReservations(): int
    {
        return $this->countReservations(true);
    }

    public function getActiveReservationsOrderedByCode(): array
    {
        $reservations = $this->getActiveReservations();

        usort($reservations, static function ($a, $b) {
            return strcmp($a->getCode(), $b->getCode());
        });

        return $reservations;
    }

    public function getUsedReservations(): array
    {
        $reservations = [];

        foreach ($this->getActiveReservations() as $reservation) {
            if ($reservation->isUsed()) {
                $reservations[] = $reservation;
            }
        }

        return $reservations;
    }

    public function getBeginDateAndTime(): \DateTime
    {
        $cacheIdentifier = 'beginDateAndTime';
        if (!array_key_exists($cacheIdentifier, $this->cache)) {
            $date = $this->date->format('m/d/Y');
            $begin = $this->begin instanceof \DateTime ? $this->begin->format('H:i') : '00:00';
            $this->cache[$cacheIdentifier] = new \DateTime($date . ' ' . $begin);
        }

        return $this->cache[$cacheIdentifier];
    }
}
