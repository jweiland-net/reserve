<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tests\Functional\Command;

use JWeiland\Reserve\Command\RemovePastPeriodsCommand;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Authentication\CommandLineUserAuthentication;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RemovePastPeriodsCommandTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/reserve'];

    protected function setUp(): void
    {
        parent::setUp();

        // override environment CLI to true
        Environment::initialize(
            Environment::getContext(),
            true,
            Environment::isComposerMode(),
            Environment::getProjectPath(),
            Environment::getPublicPath(),
            Environment::getVarPath(),
            Environment::getConfigPath(),
            Environment::getCurrentScript(),
            Environment::isUnix() ? 'UNIX' : 'WINDOWS'
        );

        Bootstrap::initializeBackendUser(CommandLineUserAuthentication::class);
        Bootstrap::initializeLanguageObject();

        $this->importDataSet(__DIR__ . '/../Fixtures/example_facility_with_period.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/activated_order_with_reservations.xml');

        // set date to past date for this tests!
        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_reserve_domain_model_period')
            ->update('tx_reserve_domain_model_period', ['date' => (new \DateTime('yesterday midnight'))->getTimestamp()], ['uid' => 1]);
    }

    /**
     * @test
     */
    public function commandRemovesOrderRelatedToPeriod(): void
    {
        $commandTester = new CommandTester(new RemovePastPeriodsCommand());
        $commandTester->execute([]);
        $orders = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_reserve_domain_model_order')
            ->select(['*'], 'tx_reserve_domain_model_order', ['uid' => 1])
            ->fetchAll();
        self::assertEquals([], $orders, 'No more order with uid 1 in database after command has been executed.');
    }

    /**
     * @test
     */
    public function commandRemovesReservationsRelatedToOrderThatWasRelatedToPeriod(): void
    {
        $commandTester = new CommandTester(new RemovePastPeriodsCommand());
        $commandTester->execute([]);
        $orders = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_reserve_domain_model_reservation')
            ->select(['*'], 'tx_reserve_domain_model_reservation', ['customer_order' => 1])
            ->fetchAll();
        self::assertEquals([], $orders, 'No more reservations related to the order that was related to the past period in database after command has been executed.');
    }
}
