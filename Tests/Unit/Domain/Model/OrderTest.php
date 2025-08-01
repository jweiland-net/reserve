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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Order::class)]
class OrderTest extends UnitTestCase
{
    protected Order $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Order();
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
        );

        parent::tearDown();
    }

    #[Test]
    public function getBookedPeriodAfterDataMapperWillReturnPeriod(): void
    {
        $period = new Period();

        $this->subject->_setProperty('bookedPeriod', $period);

        self::assertSame(
            $period,
            $this->subject->getBookedPeriod(),
        );
    }

    #[Test]
    public function setBookedPeriodWillSetBookedPeriod(): void
    {
        $period = new Period();

        $this->subject->setBookedPeriod($period);

        self::assertSame(
            $period,
            $this->subject->getBookedPeriod(),
        );
    }

    #[Test]
    public function getActivationCodeInitiallyWillReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getActivationCode(),
        );
    }

    #[Test]
    public function setActivationCodeWillSetActivationCode(): void
    {
        $activationCode = '123*abc';

        $this->subject->setActivationCode($activationCode);

        self::assertSame(
            $activationCode,
            $this->subject->getActivationCode(),
        );
    }

    #[Test]
    public function isActivatedInitiallyWillReturnFalse(): void
    {
        self::assertFalse(
            $this->subject->isActivated(),
        );
    }

    #[Test]
    public function setActivatedWillSetActivated(): void
    {
        $this->subject->setActivated(true);

        self::assertTrue(
            $this->subject->isActivated(),
        );
    }

    #[Test]
    public function getFirstNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getFirstName(),
        );
    }

    #[Test]
    public function setFirstNameSetsFirstname(): void
    {
        $this->subject->setFirstName('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getFirstName(),
        );
    }

    #[Test]
    public function getLastNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getLastName(),
        );
    }

    #[Test]
    public function setLastNameSetsLastName(): void
    {
        $this->subject->setLastName('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getLastName(),
        );
    }

    #[Test]
    public function getEmailInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getEmail(),
        );
    }

    #[Test]
    public function setEmailSetsEmail(): void
    {
        $this->subject->setEmail('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getEmail(),
        );
    }

    #[Test]
    public function getPhoneInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getPhone(),
        );
    }

    #[Test]
    public function setPhoneSetsPhone(): void
    {
        $this->subject->setPhone('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getPhone(),
        );
    }

    #[Test]
    public function getAddressInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getAddress(),
        );
    }

    #[Test]
    public function setAddressSetsAddress(): void
    {
        $this->subject->setAddress('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getAddress(),
        );
    }

    #[Test]
    public function getZipInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getZip(),
        );
    }

    #[Test]
    public function setZipSetsZip(): void
    {
        $this->subject->setZip('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getZip(),
        );
    }

    #[Test]
    public function getCityInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getCity(),
        );
    }

    #[Test]
    public function setCitySetsCity(): void
    {
        $this->subject->setCity('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getCity(),
        );
    }

    #[Test]
    public function getOrganizationInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getOrganization(),
        );
    }

    #[Test]
    public function setOrganizationSetsOrganization(): void
    {
        $this->subject->setOrganization('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getOrganization(),
        );
    }

    #[Test]
    public function getRemarksInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getRemarks(),
        );
    }

    #[Test]
    public function setRemarksSetsRemarks(): void
    {
        $this->subject->setRemarks('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getRemarks(),
        );
    }

    #[Test]
    public function getParticipantsInitiallyReturnsObjectStorage(): void
    {
        self::assertEquals(
            new ObjectStorage(),
            $this->subject->getParticipants(),
        );
    }

    #[Test]
    public function setParticipantsWithEmptyObjectStorageKeepsParticipantsUntouched(): void
    {
        $originalObjectStorage = $this->subject->getParticipants();

        $this->subject->setParticipants(new ObjectStorage());

        self::assertSame(
            $originalObjectStorage,
            $this->subject->getParticipants(),
        );
    }

    #[Test]
    public function setParticipantsWithInvalidParticipantsKeepsParticipantsUntouched(): void
    {
        $originalObjectStorage = $this->subject->getParticipants();

        $participants = new ObjectStorage();
        $participants->attach(new Participant());
        $participants->attach(new Participant());

        $this->subject->setParticipants($participants);

        self::assertSame(
            $originalObjectStorage,
            $this->subject->getParticipants(),
        );
    }

    #[Test]
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
            $this->subject->getParticipants(),
        );
    }

    #[Test]
    public function addParticipantWithNameAddsOneParticipant(): void
    {
        $participant = new Participant();
        $participant->setLastName('Weiland');

        $this->subject->addParticipant($participant);

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($participant);

        self::assertEquals(
            $objectStorage,
            $this->subject->getParticipants(),
        );
    }

    #[Test]
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
            $this->subject->getParticipants(),
        );
    }

    #[Test]
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
            $this->subject->getParticipants(),
        );
    }

    #[Test]
    public function getReservationsInitiallyReturnsObjectStorage(): void
    {
        self::assertEquals(
            new ObjectStorage(),
            $this->subject->getReservations(),
        );
    }

    #[Test]
    public function setReservationsSetsReservations(): void
    {
        $object = new Reservation();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($object);

        $this->subject->setReservations($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->subject->getReservations(),
        );
    }

    #[Test]
    public function addReservationAddsOneReservation(): void
    {
        $objectStorage = new ObjectStorage();
        $this->subject->setReservations($objectStorage);

        $object = new Reservation();
        $this->subject->addReservation($object);

        $objectStorage->attach($object);

        self::assertSame(
            $objectStorage,
            $this->subject->getReservations(),
        );
    }

    #[Test]
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
            $this->subject->getReservations(),
        );
    }

    #[Test]
    public function isCancelableWithDeactivatedOrderWillReturnTrue(): void
    {
        $this->subject->setActivated(false);

        self::assertTrue(
            $this->subject->isCancelable(),
        );
    }

    #[Test]
    public function isCancelableWithActivatedOrderAndNonCancelableFacilityWillReturnFalse(): void
    {
        $facility = new Facility();
        $facility->setCancelable(false);

        $period = new Period();
        $period->setFacility($facility);

        $this->subject->setActivated(true);
        $this->subject->setBookedPeriod($period);

        self::assertFalse(
            $this->subject->isCancelable(),
        );
    }

    #[Test]
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
            $this->subject->isCancelable(),
        );
    }

    #[Test]
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
            $this->subject->isCancelable(),
        );
    }

    #[Test]
    public function getCancelableUntilWithNonCancelableFacilityWillReturnNull(): void
    {
        $facility = new Facility();
        $facility->setCancelable(false);

        $period = new Period();
        $period->setFacility($facility);

        $this->subject->setBookedPeriod($period);

        self::assertNull(
            $this->subject->getCancelableUntil(),
        );
    }

    #[Test]
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
            $this->subject->getCancelableUntil(),
        );
    }

    public static function canBeBookedDataProvider(): array
    {
        return [
            'More participants than currently registered' => [5, true],
            'Amount of participants equals amount of current reservations' => [2, true],
            'Amount of participants is less than currently registered' => [0, false],
        ];
    }

    #[Test]
    #[DataProvider('canBeBookedDataProvider')]
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
            $this->subject->canBeBooked(),
        );
    }

    #[Test]
    public function shouldBlockFurtherOrdersForFacilityInitiallyReturnsTrue(): void
    {
        self::assertTrue(
            $this->subject->shouldBlockFurtherOrdersForFacility(),
        );
    }
}
