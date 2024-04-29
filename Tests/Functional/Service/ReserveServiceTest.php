<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tests\Functional\Service;

use JWeiland\Reserve\Service\ReserveService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ReserveServiceTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'jweiland/reserve',
    ];

    protected ReserveService $reserveService;

    protected \DateTime $testDateMidnight;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reserveService = GeneralUtility::makeInstance(ReserveService::class);

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/example_facility_with_period.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/activated_order_with_reservations.csv');

        $this->testDateMidnight = new \DateTime('+2 days midnight');

        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_reserve_domain_model_period')
            ->update(
                'tx_reserve_domain_model_period',
                ['date' => $this->testDateMidnight->getTimestamp()],
                ['deleted' => 0]
            );
    }

    protected function tearDown(): void
    {
        unset(
            $this->reserveService,
            $this->testDateMidnight
        );

        parent::tearDown();
    }

    /**
     * @test
     */
    public function getRemainingParticipantsReturnsIntInCaseOfMatch(): void
    {
        $dateAndBegin = clone $this->testDateMidnight;
        $dateAndBegin->setTime(14, 00);

        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_reserve_domain_model_period')
            ->update(
                'tx_reserve_domain_model_period',
                ['begin' => (new \DateTime('1970-01-01T14:00:00.00Z'))->getTimestamp()],
                ['uid' => 1]
            );

        self::assertSame(
            47,
            $this->reserveService->getRemainingParticipants(1, $dateAndBegin),
            'Remaining participants are returned as integer.'
        );
    }

    /**
     * @test
     */
    public function getRemainingParticipantsReturnsNullIfPeriodCouldNotBeIdentified(): void
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp(123456);

        self::assertNull(
            $this->reserveService->getRemainingParticipants(1, $dateTime),
            'Remaining participants are null because period could not be identified.'
        );
    }
}
