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
use JWeiland\Reserve\Domain\Model\Reservation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Reservation::class)]
class ReservationTest extends UnitTestCase
{
    protected Reservation $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Reservation();
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
        );

        parent::tearDown();
    }

    #[Test]
    public function setCustomerOrderSetsCustomerOrder(): void
    {
        $instance = new Order();
        $this->subject->setCustomerOrder($instance);

        self::assertSame(
            $instance,
            $this->subject->getCustomerOrder(),
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
    public function setFirstNameSetsFirstName(): void
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
    public function getCodeInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getCode(),
        );
    }

    #[Test]
    public function setCodeSetsCode(): void
    {
        $this->subject->setCode('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getCode(),
        );
    }

    #[Test]
    public function getUsedInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->subject->isUsed(),
        );
    }

    #[Test]
    public function setUsedSetsUsed(): void
    {
        $this->subject->setUsed(true);
        self::assertTrue(
            $this->subject->isUsed(),
        );
    }
}
