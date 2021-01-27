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
    protected $bookingEnd;

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

    /**
     * @var int
     */
    protected $maxParticipants = 0;

    /**
     * @var int
     */
    protected $maxParticipantsPerOrder = 0;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Reserve\Domain\Model\Order>
     */
    protected $orders;

    /**
     * @internal cache property for query results
     * @var array
     */
    protected $cache = [];

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
     * @return \DateTime|null null if no booking end was set!
     */
    public function getBookingEnd()
    {
        return $this->bookingEnd;
    }

    /**
     * @param \DateTime $bookingEnd
     */
    public function setBookingEnd(\DateTime $bookingEnd)
    {
        $this->bookingEnd = $bookingEnd;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        if ($this->date->timezone_type !== 3) {
            $this->date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        }
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
        if ($this->begin->getTimezone() !== 'UTC') {
            $this->begin->setTimezone(new \DateTimeZone('UTC'));
        }
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
        if ($this->end->getTimezone() !== 'UTC') {
            $this->end->setTimezone(new \DateTimeZone('UTC'));
        }
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

    public function getRemainingParticipants(): int
    {
        return $this->maxParticipants - $this->countReservations();
    }

    /**
     * @return int
     */
    public function getMaxParticipantsPerOrder(): int
    {
        $remaining = $this->getRemainingParticipants();
        return $this->maxParticipantsPerOrder > $remaining ? $remaining : $this->maxParticipantsPerOrder;
    }

    /**
     * Allows to iterate over the number of allowed further participants.
     *
     * @return array
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

    /**
     * @param int $maxParticipantsPerOrder
     */
    public function setMaxParticipantsPerOrder(int $maxParticipantsPerOrder)
    {
        $this->maxParticipantsPerOrder = $maxParticipantsPerOrder;
    }

    /**
     * @return ObjectStorage|Order[]
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
     * @return bool
     */
    public function getIsBookingBeginReached(): bool
    {
        return $this->isBookingBeginReached();
    }

    /**
     * @internal fluid only!
     * @return bool
     */
    public function getIsBookingTimeOver(): bool
    {
        return $this->isBookingTimeOver();
    }

    /**
     * @internal fluid only! Use isBookable() instead!
     * @return bool
     */
    public function getIsBookable(): bool
    {
        return $this->isBookable();
    }

    public function getReservations(bool $activeOnly = false): array
    {
        $reservations = [];

        foreach ($this->orders as $order) {
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
        $cacheIdentifier = 'countReservations' . ($activeOnly ? 'ActiveOnly' : '');
        if (!array_key_exists($cacheIdentifier, $this->cache)) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_reserve_domain_model_reservation');
            $queryBuilder
                ->count('r.uid')
                ->from('tx_reserve_domain_model_order', 'o')
                ->leftJoin('o', 'tx_reserve_domain_model_reservation', 'r', 'r.customer_order = o.uid')
                ->where($queryBuilder->expr()->eq(
                    'o.booked_period',
                    $queryBuilder->createNamedParameter($this->getUid())
                ));
            if ($activeOnly) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('o.activated', 1));
            }
            $this->cache[$cacheIdentifier] = (int)$queryBuilder->execute()->fetchColumn(0);
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
            $this->cache[$cacheIdentifier] = new \DateTime($this->date->format('m/d/Y') . ' ' . $this->begin->format('H:i'));
        }
        return $this->cache[$cacheIdentifier];
    }
}
