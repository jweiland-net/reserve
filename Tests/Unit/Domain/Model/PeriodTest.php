<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tests\Unit\Domain\Model;

use JWeiland\Reserve\Domain\Model\Facility;
use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Domain\Model\Period;
use JWeiland\Reserve\Domain\Model\Reservation;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \JWeiland\Reserve\Domain\Model\Period
 */
class PeriodTest extends UnitTestCase
{
    protected Period $subject;

    protected function setUp(): void
    {
        $this->subject = new Period();
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject
        );

        parent::tearDown();
    }

    /**
     * @test
     */
    public function getFacilityAfterDataMapperWillReturnFacility(): void
    {
        $facility = new Facility();

        $this->subject->_setProperty('facility', $facility);

        self::assertSame(
            $facility,
            $this->subject->getFacility()
        );
    }

    /**
     * @test
     */
    public function setFacilitySetsFacility(): void
    {
        $instance = new Facility();
        $this->subject->setFacility($instance);

        self::assertSame(
            $instance,
            $this->subject->getFacility()
        );
    }

    /**
     * @test
     */
    public function getBookingBeginAfterDataMapperWillReturnDateTime(): void
    {
        $bookingBegin = new \DateTime('now');

        $this->subject->_setProperty('bookingBegin', $bookingBegin);

        self::assertSame(
            $bookingBegin,
            $this->subject->getBookingBegin()
        );
    }

    /**
     * @test
     */
    public function setBookingBeginSetsBookingBegin(): void
    {
        $bookingBegin = new \DateTime('now');
        $this->subject->setBookingBegin($bookingBegin);

        self::assertSame(
            $bookingBegin,
            $this->subject->getBookingBegin()
        );
    }

    /**
     * @test
     */
    public function getBookingEndInitiallyReturnsNull(): void
    {
        self::assertNull(
            $this->subject->getBookingEnd()
        );
    }

    /**
     * @test
     */
    public function setBookingEndSetsBookingEnd(): void
    {
        $date = new \DateTime();
        $this->subject->setBookingEnd($date);

        self::assertSame(
            $date,
            $this->subject->getBookingEnd()
        );
    }

    /**
     * @test
     */
    public function setDateSetsDate(): void
    {
        $date = new \DateTime();
        $this->subject->setDate($date);

        self::assertSame(
            $date,
            $this->subject->getDate()
        );
    }

    /**
     * @test
     */
    public function setBeginSetsBegin(): void
    {
        $date = new \DateTime();
        $this->subject->setBegin($date);

        self::assertSame(
            $date,
            $this->subject->getBegin()
        );
    }

    /**
     * @test
     */
    public function setEndSetsEnd(): void
    {
        $date = new \DateTime();
        $this->subject->setEnd($date);

        self::assertSame(
            $date,
            $this->subject->getEnd()
        );
    }

    /**
     * @test
     */
    public function getMaxParticipantsInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->subject->getMaxParticipants()
        );
    }

    /**
     * @test
     */
    public function setMaxParticipantsSetsMaxParticipants(): void
    {
        $this->subject->setMaxParticipants(123456);

        self::assertSame(
            123456,
            $this->subject->getMaxParticipants()
        );
    }

    /**
     * @test
     */
    public function setMaxParticipantsWithNegativeValueSetsMaxParticipantsToZero(): void
    {
        $this->subject->setMaxParticipants(-12);

        self::assertSame(
            0,
            $this->subject->getMaxParticipants()
        );
    }

    /**
     * @test
     */
    public function getOrdersInitiallyReturnsObjectStorage(): void
    {
        self::assertEquals(
            new ObjectStorage(),
            $this->subject->getOrders()
        );
    }

    /**
     * @test
     */
    public function setOrdersSetsOrders(): void
    {
        $object = new Order();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($object);
        $this->subject->setOrders($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->subject->getOrders()
        );
    }

    /**
     * @test
     */
    public function addOrderAddsOneOrder(): void
    {
        $objectStorage = new ObjectStorage();
        $this->subject->setOrders($objectStorage);

        $object = new Order();
        $this->subject->addOrder($object);

        $objectStorage->attach($object);

        self::assertSame(
            $objectStorage,
            $this->subject->getOrders()
        );
    }

    /**
     * @test
     */
    public function removeOrderRemovesOneOrder(): void
    {
        $object = new Order();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($object);
        $this->subject->setOrders($objectStorage);

        $this->subject->removeOrder($object);
        $objectStorage->detach($object);

        self::assertSame(
            $objectStorage,
            $this->subject->getOrders()
        );
    }

    /**
     * @test
     */
    public function isBookableWithValidBookingBeginWillReturnTrue(): void
    {
        $this->subject->setBookingBegin(new \DateTime('yesterday'));

        self::assertTrue(
            $this->subject->isBookable()
        );
    }

    /**
     * @test
     */
    public function isBookableWithValidBookingBeginAndBookingEndWillReturnTrue(): void
    {
        $this->subject->setBookingBegin(new \DateTime('yesterday'));
        $this->subject->setBookingEnd(new \DateTime('tomorrow'));

        self::assertTrue(
            $this->subject->isBookable()
        );
    }

    /**
     * @test
     */
    public function isBookableWithBookingBeginInFutureWillReturnFalse(): void
    {
        $this->subject->setBookingBegin(new \DateTime('tomorrow'));

        self::assertFalse(
            $this->subject->isBookable()
        );
    }

    /**
     * @test
     */
    public function isBookableWithBookingEndInPastWillReturnFalse(): void
    {
        $this->subject->setBookingBegin(new \DateTime('yesterday'));
        $this->subject->setBookingEnd(new \DateTime('yesterday'));

        self::assertFalse(
            $this->subject->isBookable()
        );
    }

    /**
     * @test
     */
    public function isBookingBeginReachedWillReturnTrue(): void
    {
        $this->subject->setBookingBegin(new \DateTime('yesterday'));

        self::assertTrue(
            $this->subject->isBookable()
        );
    }

    /**
     * @test
     */
    public function isBookingBeginReachedWillReturnFalse(): void
    {
        $this->subject->setBookingBegin(new \DateTime('tomorrow'));

        self::assertFalse(
            $this->subject->isBookable()
        );
    }

    /**
     * @test
     */
    public function isBookingTimeOverWillReturnTrue(): void
    {
        $this->subject->setBookingEnd(new \DateTime('yesterday'));

        self::assertTrue(
            $this->subject->isBookingTimeOver()
        );
    }

    /**
     * @test
     */
    public function isBookingTimeOverWillReturnFalse(): void
    {
        $this->subject->setBookingEnd(new \DateTime('tomorrow'));

        self::assertFalse(
            $this->subject->isBookingTimeOver()
        );
    }

    /**
     * @test
     */
    public function getReservationsWithZeroOrdersWillReturnEmptyArray(): void
    {
        self::assertSame(
            [],
            $this->subject->getReservations()
        );
    }

    /**
     * @test
     */
    public function getReservationsWithOrdersWillReturnAllReservations(): void
    {
        $expectedReservations = [
            new Reservation(),
            new Reservation(),
            new Reservation(),
        ];

        $order1 = new Order();
        $order1->addReservation($expectedReservations[0]);

        $order2 = new Order();
        $order2->addReservation($expectedReservations[1]);
        $order2->addReservation($expectedReservations[2]);

        $this->subject->addOrder($order1);
        $this->subject->addOrder($order2);

        self::assertSame(
            $expectedReservations,
            $this->subject->getReservations()
        );
    }

    /**
     * @test
     */
    public function getReservationsWithOrdersWillReturnActiveReservations(): void
    {
        $reservation1 = new Reservation();
        $reservation2 = new Reservation();
        $reservation3 = new Reservation();

        $order1 = new Order();
        $order1->setActivated(false);
        $order1->addReservation($reservation1);

        $order2 = new Order();
        $order2->setActivated(true);
        $order2->addReservation($reservation2);
        $order2->addReservation($reservation3);

        $this->subject->addOrder($order1);
        $this->subject->addOrder($order2);

        self::assertSame(
            [
                $reservation2,
                $reservation3,
            ],
            $this->subject->getReservations(true)
        );
    }

    /**
     * @test
     */
    public function getUsedReservationsWillReturnUsedReservations(): void
    {
        $reservation1 = new Reservation();
        $reservation1->setUsed(false);
        $reservation2 = new Reservation();
        $reservation2->setUsed(false);
        $reservation3 = new Reservation();
        $reservation3->setUsed(true);

        $order1 = new Order();
        $order1->setActivated(false);
        $order1->addReservation($reservation1);

        $order2 = new Order();
        $order2->setActivated(true);
        $order2->addReservation($reservation2);
        $order2->addReservation($reservation3);

        $this->subject->addOrder($order1);
        $this->subject->addOrder($order2);

        self::assertSame(
            [
                $reservation3,
            ],
            $this->subject->getUsedReservations()
        );
    }

    /**
     * @test
     */
    public function getBeginDateAndTimeWillReturnCombinedDateTime(): void
    {
        $this->subject->setDate(new \DateTime('now'));
        $this->subject->setBegin(new \DateTime('tomorrow 15:14:13'));

        self::assertEquals(
            new \DateTime('now 15:14:00'),
            $this->subject->getBeginDateAndTime()
        );
    }

    /**
     * @test
     */
    public function getBeginDateAndTimeWithEmptyBeginWillReturnCombinedDateTimeAtMidnight(): void
    {
        $this->subject->setDate(new \DateTime('now'));

        self::assertEquals(
            new \DateTime('now 00:00'),
            $this->subject->getBeginDateAndTime()
        );
    }
}
