<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tests\Functional\Repository;

use JWeiland\Reserve\Domain\Model\Period;
use JWeiland\Reserve\Domain\Repository\PeriodRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class PeriodRepositoryTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'jweiland/reserve',
    ];

    protected PeriodRepository $periodRepository;

    protected \DateTime $testDateMidnight;

    protected function setUp(): void
    {
        parent::setUp();

        $this->periodRepository = GeneralUtility::makeInstance(PeriodRepository::class);

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
            $this->periodRepository,
            $this->testDateMidnight
        );

        parent::tearDown();
    }

    /**
     * @test
     */
    public function findByDateAndBeginReturnsPeriodsForGivenDateTime(): void
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
            1,
            $this->periodRepository->findByDateAndBegin($dateAndBegin, 1)->getFirst()->getUid(),
            'findByDateTime() returns matching period with uid 1'
        );
    }

    /**
     * @test
     */
    public function findByDateAndBeginReturnsEmptyQueryResult(): void
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp(123456);

        self::assertSame(
            0,
            $this->periodRepository->findByDateAndBegin($dateTime, 1)->count(),
            'findByDateTime() returns empty QueryResult if there is no matching period'
        );
    }

    /**
     * @test
     */
    public function findUpcomingAndRunningByFacilityUidsReturnsFilledQueryResult(): void
    {
        self::assertSame(
            3,
            $this->periodRepository->findUpcomingAndRunningByFacilityUids([1])->count()
        );
    }

    /**
     * @test
     */
    public function findUpcomingAndRunningByFacilityUidUsesDateAndBeginAsOrdering(): void
    {
        self::assertSame(
            [1, 3, 2],
            array_map(
                static function (Period $period) {
                    return $period->getUid();
                },
                $this->periodRepository->findUpcomingAndRunningByFacilityUids([1])->toArray()
            ),
            'findUpcomingAndRunningByFacilityUids() uses date and begin as ordering for result'
        );
    }
}
