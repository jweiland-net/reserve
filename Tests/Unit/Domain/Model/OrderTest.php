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
use JWeiland\Reserve\Domain\Model\Participant;
use JWeiland\Reserve\Domain\Model\Period;
use JWeiland\Reserve\Domain\Model\Reservation;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \JWeiland\Reserve\Domain\Model\Order
 */
class OrderTest extends UnitTestCase
{
    protected Order $subject;

    protected function setUp(): void
    {
        $this->subject = new Order();
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
    public function getBookedPeriodAfterDataMapperWillReturnPeriod(): void
    {
        $period = new Period();

        $this->subject->_setProperty('bookedPeriod', $period);

        self::assertSame(
            $period,
            $this->subject->getBookedPeriod()
        );
    }

    /**
     * @test
     */
    public function setBookedPeriodWillSetBookedPeriod(): void
    {
        $period = new Period();

        $this->subject->setBookedPeriod($period);

        self::assertSame(
            $period,
            $this->subject->getBookedPeriod()
        );
    }

    /**
     * @test
     */
    public function getActivationCodeInitiallyWillReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getActivationCode()
        );
    }

    /**
     * @test
     */
    public function setActivationCodeWillSetActivationCode(): void
    {
        $activationCode = '123*abc';

        $this->subject->setActivationCode($activationCode);

        self::assertSame(
            $activationCode,
            $this->subject->getActivationCode()
        );
    }

    /**
     * @test
     */
    public function isActivatedInitiallyWillReturnFalse(): void
    {
        self::assertFalse(
            $this->subject->isActivated()
        );
    }

    /**
     * @test
     */
    public function setActivatedWillSetActivated(): void
    {
        $this->subject->setActivated(true);

        self::assertTrue(
            $this->subject->isActivated()
        );
    }

    /**
     * @test
     */
    public function getFirstNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getFirstName()
        );
    }

    /**
     * @test
     */
    public function setFirstNameSetsFirstname(): void
    {
        $this->subject->setFirstName('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getFirstName()
        );
    }

    /**
     * @test
     */
    public function getLastNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getLastName()
        );
    }

    /**
     * @test
     */
    public function setLastNameSetsLastName(): void
    {
        $this->subject->setLastName('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getLastName()
        );
    }

    /**
     * @test
     */
    public function getEmailInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getEmail()
        );
    }

    /**
     * @test
     */
    public function setEmailSetsEmail(): void
    {
        $this->subject->setEmail('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getEmail()
        );
    }

    /**
     * @test
     */
    public function getPhoneInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getPhone()
        );
    }

    /**
     * @test
     */
    public function setPhoneSetsPhone(): void
    {
        $this->subject->setPhone('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getPhone()
        );
    }

    /**
     * @test
     */
    public function getAddressInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getAddress()
        );
    }

    /**
     * @test
     */
    public function setAddressSetsAddress(): void
    {
        $this->subject->setAddress('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getAddress()
        );
    }

    /**
     * @test
     */
    public function getZipInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getZip()
        );
    }

    /**
     * @test
     */
    public function setZipSetsZip(): void
    {
        $this->subject->setZip('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getZip()
        );
    }

    /**
     * @test
     */
    public function getCityInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getCity()
        );
    }

    /**
     * @test
     */
    public function setCitySetsCity(): void
    {
        $this->subject->setCity('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getCity()
        );
    }

    /**
     * @test
     */
    public function getOrganizationInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getOrganization()
        );
    }

    /**
     * @test
     */
    public function setOrganizationSetsOrganization(): void
    {
        $this->subject->setOrganization('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getOrganization()
        );
    }

    /**
     * @test
     */
    public function getRemarksInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getRemarks()
        );
    }

    /**
     * @test
     */
    public function setRemarksSetsRemarks(): void
    {
        $this->subject->setRemarks('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getRemarks()
        );
    }

    /**
     * @test
     */
    public function getParticipantsInitiallyReturnsObjectStorage(): void
    {
        self::assertEquals(
            new ObjectStorage(),
            $this->subject->getParticipants()
        );
    }

    /**
     * @test
     */
    public function setParticipantsWithEmptyObjectStorageKeepsParticipantsUntouched(): void
    {
        $originalObjectStorage = $this->subject->getParticipants();

        $this->subject->setParticipants(new ObjectStorage());

        self::assertSame(
            $originalObjectStorage,
            $this->subject->getParticipants()
        );
    }

    /**
     * @test
     */
    public function setParticipantsWithInvalidParticipantsKeepsParticipantsUntouched(): void
    {
        $originalObjectStorage = $this->subject->getParticipants();

        $participants = new ObjectStorage();
        $participants->attach(new Participant());
        $participants->attach(new Participant());

        $this->subject->setParticipants($participants);

        self::assertSame(
            $originalObjectStorage,
            $this->subject->getParticipants()
        );
    }

    /**
     * @test
     */
    public function setParticipantsWithValidParticipantsSetsParticipants(): void
    {
        $participant1 = new Participant();
        $participant1->setFirstName('Jochen');
        $participant2 = new Participant();
        $participant2->setLastName('Weiland');

        $participants = new ObjectStorage();
        $participants->attach($participant1);
        $participants->attach($participant2);

        $this->subject->setParticipants($participants);

        self::assertEquals(
            $participants,
            $this->subject->getParticipants()
        );
    }

    /**
     * @test
     */
    public function addParticipantWithNameAddsOneParticipant(): void
    {
        $participant = new Participant();
        $participant->setLastName('Weiland');
        $this->subject->addParticipant($participant);

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($participant);

        self::assertEquals(
            $objectStorage,
            $this->subject->getParticipants()
        );
    }

    /**
     * @test
     */
    public function addParticipantWithoutNameKeepsParticipantsUntouched(): void
    {
        $validParticipant = new Participant();
        $validParticipant->setLastName('Weiland');

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($validParticipant);

        $this->subject->setParticipants($objectStorage);

        $originalObjectStorage = $this->subject->getParticipants();

        // try adding invalid participant to existing collection
        $this->subject->addParticipant(new Participant());

        self::assertSame(
            $originalObjectStorage,
            $this->subject->getParticipants()
        );
    }

    /**
     * @test
     */
    public function removeParticipantRemovesOneParticipant(): void
    {
        $participant1 = new Participant();
        $participant1->setFirstName('Jochen');
        $participant2 = new Participant();
        $participant2->setLastName('Weiland');

        $participants = new ObjectStorage();
        $participants->attach($participant1);
        $participants->attach($participant2);

        $this->subject->setParticipants($participants);

        $this->subject->removeParticipant($participant1);
        $participants->detach($participant1);

        self::assertEquals(
            $participants,
            $this->subject->getParticipants()
        );
    }

    /**
     * @test
     */
    public function getReservationsInitiallyReturnsObjectStorage(): void
    {
        self::assertEquals(
            new ObjectStorage(),
            $this->subject->getReservations()
        );
    }

    /**
     * @test
     */
    public function setReservationsSetsReservations(): void
    {
        $object = new Reservation();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($object);
        $this->subject->setReservations($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->subject->getReservations()
        );
    }

    /**
     * @test
     */
    public function addReservationAddsOneReservation(): void
    {
        $objectStorage = new ObjectStorage();
        $this->subject->setReservations($objectStorage);

        $object = new Reservation();
        $this->subject->addReservation($object);

        $objectStorage->attach($object);

        self::assertSame(
            $objectStorage,
            $this->subject->getReservations()
        );
    }

    /**
     * @test
     */
    public function removeReservationRemovesOneReservation(): void
    {
        $object = new Reservation();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($object);
        $this->subject->setReservations($objectStorage);

        $this->subject->removeReservation($object);
        $objectStorage->detach($object);

        self::assertSame(
            $objectStorage,
            $this->subject->getReservations()
        );
    }

    /**
     * @test
     */
    public function isCancelableWithDeactivatedOrderWillReturnTrue(): void
    {
        $this->subject->setActivated(false);

        self::assertTrue(
            $this->subject->isCancelable()
        );
    }

    /**
     * @test
     */
    public function isCancelableWithActivatedOrderAndNonCancelableFacilityWillReturnFalse(): void
    {
        $facility = new Facility();
        $facility->setCancelable(false);

        $period = new Period();
        $period->setFacility($facility);

        $this->subject->setActivated(true);
        $this->subject->setBookedPeriod($period);

        self::assertFalse(
            $this->subject->isCancelable()
        );
    }

    /**
     * @test
     */
    public function isCancelableWithActivatedOrderAndCancelableFacilityInFutureWillReturnTrue(): void
    {
        // Only the date of \DateTime will be used
        $startDateOfTheEvent = new \DateTime('+2 days');
        // Only the time of \DateTime will be used
        $startTimeOfTheEvent = new \DateTime('+2 days 15:00:00');

        $facility = new Facility();
        $facility->setCancelableUntilMinutes(120); // cancelable until 13 o'clock
        $facility->setCancelable(true);

        $period = new Period();
        $period->setDate($startDateOfTheEvent);
        $period->setBegin($startTimeOfTheEvent);
        $period->setFacility($facility);

        $this->subject->setActivated(true);
        $this->subject->setBookedPeriod($period);

        self::assertTrue(
            $this->subject->isCancelable()
        );
    }

    /**
     * @test
     */
    public function isCancelableWithActivatedOrderAndCancelableFacilityInPastWillReturnFalse(): void
    {
        // Only the date of \DateTime will be used
        $startDateOfTheEvent = new \DateTime('-2 days');
        // Only the time of \DateTime will be used
        $startTimeOfTheEvent = new \DateTime('-2 days 15:00:00');

        $facility = new Facility();
        $facility->setCancelableUntilMinutes(120); // cancelable until 13 o'clock
        $facility->setCancelable(true);

        $period = new Period();
        $period->setDate($startDateOfTheEvent);
        $period->setBegin($startTimeOfTheEvent);
        $period->setFacility($facility);

        $this->subject->setActivated(true);
        $this->subject->setBookedPeriod($period);

        self::assertFalse(
            $this->subject->isCancelable()
        );
    }

    /**
     * @test
     */
    public function getCancelableUntilWithNonCancelableFacilityWillReturnNull(): void
    {
        $facility = new Facility();
        $facility->setCancelable(false);

        $period = new Period();
        $period->setFacility($facility);

        $this->subject->setBookedPeriod($period);

        self::assertNull(
            $this->subject->getCancelableUntil()
        );
    }

    /**
     * @test
     */
    public function getCancelableUntilWithCancelableFacilityWillReturnDateTime(): void
    {
        // Only the date of \DateTime will be used
        $startDateOfTheEvent = new \DateTime('+2 days');
        // Only the time of \DateTime will be used
        $startTimeOfTheEvent = new \DateTime('+2 days 15:00:00');

        $facility = new Facility();
        $facility->setCancelableUntilMinutes(120); // cancelable until 13 o'clock
        $facility->setCancelable(true);

        $period = new Period();
        $period->setDate($startDateOfTheEvent);
        $period->setBegin($startTimeOfTheEvent);
        $period->setFacility($facility);

        $this->subject->setBookedPeriod($period);

        self::assertEquals(
            new \DateTime('+2 days 13:00:00'),
            $this->subject->getCancelableUntil()
        );
    }

    public function canBeBookedDataProvider(): array
    {
        return [
            'More participants than currently registered' => [5, true],
            'Amount of participants equals amount of current reservations' => [2, true],
            'Amount of participants is less than currently registered' => [0, false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider canBeBookedDataProvider
     */
    public function canBeBookedWithFreePlacesWillReturnTrue(int $maxParticipantsPerOrder, bool $expectedResult): void
    {
        $participant1 = new Participant();
        $participant1->setFirstName('Jochen');
        $participant2 = new Participant();
        $participant2->setLastName('Weiland');

        $participants = new ObjectStorage();
        $participants->attach($participant1);
        $participants->attach($participant2);

        $this->subject->setParticipants($participants);

        $periodMock = $this->createMock(Period::class);
        $periodMock
            ->expects(self::atLeastOnce())
            ->method('getMaxParticipantsPerOrder')
            ->willReturn($maxParticipantsPerOrder);

        $this->subject->setBookedPeriod($periodMock);

        self::assertSame(
            $expectedResult,
            $this->subject->canBeBooked()
        );
    }

    /**
     * @test
     */
    public function shouldBlockFurtherOrdersForFacilityInitiallyReturnsTrue(): void
    {
        self::assertTrue(
            $this->subject->shouldBlockFurtherOrdersForFacility()
        );
    }
}
