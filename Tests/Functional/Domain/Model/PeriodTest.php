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
use Nimut\TestingFramework\TestCase\FunctionalTestCase;

/**
 * @testdox An period
 * @covers \JWeiland\Reserve\Domain\Model\Period
 */
class PeriodTest extends FunctionalTestCase
{
    /**
     * @var Period
     */
    protected $subject;

    /*
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/reserve'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Period();
        $this->subject->_setProperty('uid', 1);
    }

    protected function tearDown(): void
    {
        unset($this->subject);

        parent::tearDown();
    }

    /**
     * @test
     */
    public function countReservationsWillReturnAmountOfActivatedReservations(): void
    {
        $this->importDataSet(__DIR__ . '/../../Fixtures/activated_order_with_reservations.xml');

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
        $this->importDataSet(__DIR__ . '/../../Fixtures/non_activated_order_with_reservations.xml');

        self::assertSame(
            2,
            $this->subject->countReservations(false)
        );
    }
}
