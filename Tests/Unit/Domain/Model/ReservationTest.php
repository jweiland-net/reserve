<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tests\Unit\Domain\Model;

use JWeiland\Reserve\Domain\Model\Reservation;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * @testdox An reservation
 * @covers JWeiland\Reserve\Domain\Model\Reservation
 */
class ReservationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function canReceiveAFirstName()
    {
        $subject = new Reservation();
        $subject->setFirstName('First Name');

        self::assertSame('First Name', $subject->getFirstName());
    }

    /**
     * @test
     */
    public function canReceiveALastName()
    {
        $subject = new Reservation();
        $subject->setLastName('Last Name');

        self::assertSame('Last Name', $subject->getLastName());
    }
}
