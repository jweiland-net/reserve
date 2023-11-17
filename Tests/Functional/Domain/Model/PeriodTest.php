<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tests\Functional\Domain\Model;

use JWeiland\Reserve\Domain\Model\Period;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \JWeiland\Reserve\Domain\Model\Period
 */
class PeriodTest extends FunctionalTestCase
{
    protected Period $subject;

    protected array $testExtensionsToLoad = [
        'jweiland/reserve',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Period();
        $this->subject->_setProperty('uid', 1);
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject
        );

        parent::tearDown();
    }

    public function remainingParticipantsDataProvider(): array
    {
        return [
            'high amount of max participants' => [50, 47],
            'max participants a little bit higher than amount of reservations' => [5, 2],
            'max participants equals amount of reservations' => [3, 0],
            'max participants less than amount of reservations' => [1, 0],
            'negative max participants' => [-12, 0],
        ];
    }

    /**
     * @test
     *
     * @dataProvider remainingParticipantsDataProvider
     */
    public function getRemainingParticipants(int $maxParticipants, int $expectedResult): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/activated_order_with_reservations.csv');

        $this->subject->setMaxParticipants($maxParticipants);

        self::assertSame(
            $expectedResult,
            $this->subject->getRemainingParticipants()
        );
    }

    public function maxParticipantsPerOrderDataProvider(): array
    {
        return [
            'max participants per order equals max participants' => [50, 47],
            'max participants per order equals amount of remaining reservations' => [47, 47],
            'max participants per order less than amount of reservations' => [1, 1],
            'negative max participants per order' => [-15, 0],
        ];
    }

    /**
     * @test
     *
     * @dataProvider maxParticipantsPerOrderDataProvider
     */
    public function getMaxParticipantsPerOrder(int $maxParticipantsForOrder, int $expectedResult): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/activated_order_with_reservations.csv');

        $this->subject->setMaxParticipants(50);
        $this->subject->setMaxParticipantsPerOrder($maxParticipantsForOrder);

        self::assertSame(
            $expectedResult,
            $this->subject->getMaxParticipantsPerOrder()
        );
    }

    /**
     * @test
     */
    public function countReservationsWillReturnAmountOfActivatedReservations(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/activated_order_with_reservations.csv');

        self::assertSame(
            3,
            $this->subject->countReservations(true)
        );
    }

    /**
     * @test
     */
    public function countReservationsWillReturnAmountOfAllReservations(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/non_activated_order_with_reservations.csv');

        self::assertSame(
            2,
            $this->subject->countReservations()
        );
    }
}
