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
}
