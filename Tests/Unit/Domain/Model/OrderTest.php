<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tests\Unit\Domain\Model;

use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Domain\Model\Participant;
use JWeiland\Reserve\Domain\Model\Period;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * @testdox An order
 * @covers JWeiland\Reserve\Domain\Model\Order
 * @uses JWeiland\Reserve\Domain\Model\Participant
 */
class OrderTest extends UnitTestCase
{
    /**
     * @test
     */
    public function onlyAddsParticipantsWithAName()
    {
        $participants = new ObjectStorage();
        $participant1 = new Participant();
        $participant1->setLastName('Last Name');
        $participants->attach($participant1);
        $participant2 = new Participant();
        $participant2->setFirstName('First Name');
        $participants->attach($participant2);
        $participants->attach(new Participant());

        $subject = new Order();
        $subject->setParticipants($participants);

        self::assertCount(2, $subject->getParticipants());
    }

    /**
     * @test
     */
    public function canBeBookedIfMaxParticipantsPerOrderIsNotReached()
    {
        $subject = new Order();

        self::assertTrue($subject->shouldBlockFurtherOrdersForFacility());
    }

    /**
     * @test
     */
    public function canNotBeBookedIfMaxParticipantsAreExceeded()
    {
        $period = $this->prophesize(Period::class);
        $period->getMaxParticipantsPerOrder()->willReturn(2);
        $participants = $this->prophesize(ObjectStorage::class);
        $participants->count()->willReturn(3);

        $subject = new Order();
        $subject->setBookedPeriod($period->reveal());
        $this->forceProperty($subject, 'participants', $participants->reveal());

        self::assertFalse($subject->canBeBooked());
    }

    /**
     * @test
     */
    public function alwaysBlocksFurtherOrderForFacility()
    {
        $subject = new Order();

        self::assertTrue($subject->shouldBlockFurtherOrdersForFacility());
    }

    private function forceProperty($subject, string $name, $value)
    {
        $objectReflection = new \ReflectionObject($subject);
        $property = $objectReflection->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($subject, $value);
    }
}
