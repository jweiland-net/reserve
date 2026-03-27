<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tests\Functional\ViewHelpers;

use JWeiland\Reserve\Domain\Repository\PeriodRepository;
use JWeiland\Reserve\Service\ReserveService;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class PeriodRegistrationViewHelperTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'jweiland/reserve',
    ];

    private const BASE_TEMPLATE_PATH = 'EXT:reserve/Tests/Functional/ViewHelpers/Fixtures';

    protected ViewInterface $view;

    protected \DateTime $testDateMidnight;

    protected \DateTime $testDateAndBegin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/example_facility_with_period.csv');

        $this->testDateMidnight = new \DateTime('+2 days midnight');
        $this->testDateAndBegin = clone $this->testDateMidnight;
        $this->testDateAndBegin->setTime(14, 00);

        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_reserve_domain_model_period')
            ->update(
                'tx_reserve_domain_model_period',
                ['date' => $this->testDateMidnight->getTimestamp()],
                ['deleted' => 0],
            );

        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_reserve_domain_model_period')
            ->update(
                'tx_reserve_domain_model_period',
                ['begin' => (new \DateTime('1970-01-01T14:00:00.00Z'))->getTimestamp()],
                ['uid' => 1],
            );

        $this->view = $this->createView();
        $this->view->assign('facilityUid', 1);
        $this->view->assign('dateAndBegin', $this->testDateAndBegin->getTimestamp());
    }

    protected function tearDown(): void
    {
        unset(
            $this->view,
            $this->testDateMidnight,
            $this->testDateAndBegin,
        );

        parent::tearDown();
    }

    #[Test]
    public function viewHelperUsesReserveServiceToFindPeriod(): void
    {
        $reserveServiceMock = $this->createMock(ReserveService::class);
        $reserveServiceMock
            ->expects(self::exactly(1))
            ->method('findPeriodsByDateAndBegin')
            ->with(
                self::equalTo(1),
                self::equalTo($this->testDateAndBegin),
            )
            ->willReturn([]);

        GeneralUtility::setSingletonInstance(ReserveService::class, $reserveServiceMock);

        $this->view->render('remainingParticipants_periodRegistrationViewHelper');
    }

    #[Test]
    public function viewHelperSetsPeriodsAndRendersRemainingParticipants(): void
    {
        $remainingParticipants = GeneralUtility::makeInstance(PeriodRepository::class)
            ->findByUid(1)
            ->getRemainingParticipants();

        self::assertStringContainsString(
            sprintf('<p>Remaining participants: %d</p>', $remainingParticipants),
            $this->view->render('remainingParticipants_periodRegistrationViewHelper'),
            'ViewHelper renders remaining participants if facility and period date match.',
        );
    }

    #[Test]
    public function viewHelperSetsPeriodsAndRendersInfoThatNoPeriodWasFound(): void
    {
        $this->view->assign('dateAndBegin', (new \DateTime('123456'))->getTimestamp());

        self::assertStringContainsString(
            'Could not find any period for given time.',
            $this->view->render('remainingParticipants_periodRegistrationViewHelper'),
            'ViewHelper renders info that no period was found.',
        );
    }

    #[Test]
    public function viewHelperSetsPeriodsToCustomVariableName(): void
    {
        self::assertStringContainsString(
            'Test',
            $this->view->render('customVariableName_periodRegistrationViewHelper'),
            'ViewHelper sets periods to custom variable name',
        );
    }

    protected function createView(): ViewInterface
    {
        $viewFactoryData = new ViewFactoryData(
            templateRootPaths: [self::BASE_TEMPLATE_PATH],
        );

        return GeneralUtility::makeInstance(ViewFactoryInterface::class)->create($viewFactoryData);
    }
}
