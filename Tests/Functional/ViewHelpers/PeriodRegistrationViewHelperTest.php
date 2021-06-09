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
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Prophecy\Argument;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

class PeriodRegistrationViewHelperTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/reserve'];

    /**
     * @var StandaloneView
     */
    protected $standaloneView;

    /**
     * @var \DateTime
     */
    protected $testDateMidnight;

    /**
     * @var \DateTime
     */
    protected $testDateAndBegin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->standaloneView = GeneralUtility::makeInstance(ObjectManager::class)->get(StandaloneView::class);

        $this->importDataSet(__DIR__ . '/../Fixtures/example_facility_with_period.xml');

        $this->testDateMidnight = new \DateTime('+2 days midnight');
        $this->testDateAndBegin = clone $this->testDateMidnight;
        $this->testDateAndBegin->setTime(14, 00);

        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_reserve_domain_model_period')
            ->update(
                'tx_reserve_domain_model_period',
                ['date' => $this->testDateMidnight->getTimestamp()],
                ['deleted' => 0]
            );

        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_reserve_domain_model_period')
            ->update(
                'tx_reserve_domain_model_period',
                ['begin' => (new \DateTime('1970-01-01T14:00:00.00Z'))->getTimestamp()],
                ['uid' => 1]
            );

        $this->setTestTemplate(1, $this->testDateAndBegin);
    }

    protected function setTestTemplate(int $facilityUid, \DateTime $dateAndBegin): void
    {
        $this->standaloneView->setTemplateSource(
            <<<TEMPLATE
{namespace jw=JWeiland\Reserve\ViewHelpers}
<jw:periodRegistration facilityUid="${facilityUid}" dateAndBegin="{$dateAndBegin->getTimestamp()}">
	<f:if condition="{periods}">
		<f:then>
			<f:for each="{periods}" as="period">
				<p>Remaining participants: {period.remainingParticipants}</p>
			</f:for>
		</f:then>
		<f:else>
			Could not find any period for given time.
		</f:else>
	</f:if>
</jw:periodRegistration>
TEMPLATE
        );
    }

    /**
     * @test
     */
    public function viewHelperUsesReserveServiceToFindPeriod(): void
    {
        $reserveServiceProphecy = $this->prophesize(ReserveService::class);
        $reserveServiceProphecy->findPeriodsByDateAndBegin(Argument::exact(1), Argument::exact($this->testDateAndBegin))->willReturn([])->shouldBeCalledTimes(1);

        GeneralUtility::setSingletonInstance(ReserveService::class, $reserveServiceProphecy->reveal());
        $this->standaloneView->render();
    }

    /**
     * @test
     */
    public function viewHelperSetsPeriodsAndRendersRemainingParticipants(): void
    {
        $remainingParticipants = GeneralUtility::makeInstance(ObjectManager::class)->get(PeriodRepository::class)->findByUid(1)->getRemainingParticipants();
        self::assertContains(
            sprintf('<p>Remaining participants: %d</p>', $remainingParticipants),
            $this->standaloneView->render(),
            'ViewHelper renders remaining participants if facility and period date match.'
        );
    }

    /**
     * @test
     */
    public function viewHelperSetsPeriodsAndRendersInfoThatNoPeriodWasFound(): void
    {
        $this->setTestTemplate(1, new \DateTime('123456'));
        self::assertContains(
            'Could not find any period for given time.',
            $this->standaloneView->render(),
            'ViewHelper renders remainfo that no period was found.'
        );
    }
}
