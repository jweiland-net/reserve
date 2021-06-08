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
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ReserveServiceTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/reserve'];

    /**
     * @var ReserveService
     */
    protected $reserveService;

    protected function setUp()
    {
        parent::setUp();
        $this->reserveService = GeneralUtility::makeInstance(ObjectManager::class)->get(ReserveService::class);

        $this->importDataSet(__DIR__ . '/../Fixtures/example_facility_with_period.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/activated_order_with_reservations.xml');
    }

    /**
     * @test
     */
    public function getRemainingParticipantsReturnsIntInCaseOfMatch(): void
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp(2051269200);
        self::assertSame(
            47,
            $this->reserveService->getRemainingParticipants(1, $dateTime),
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
