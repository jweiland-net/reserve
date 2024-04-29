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
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Authentication\CommandLineUserAuthentication;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class RemovePastPeriodsCommandTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'jweiland/reserve',
    ];

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

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/example_facility_with_period.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/activated_order_with_reservations.csv');

        // set date to past date for these tests!
        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_reserve_domain_model_period')
            ->update(
                'tx_reserve_domain_model_period',
                ['date' => (new \DateTime('yesterday midnight'))->getTimestamp()],
                ['uid' => 1]
            );
    }

    /**
     * @test
     */
    public function commandRemovesOrderRelatedToPeriod(): void
    {
        // Create an instance of the command and inject dependencies
        $command = GeneralUtility::makeInstance(RemovePastPeriodsCommand::class);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $orders = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_reserve_domain_model_order')
            ->select(['*'], 'tx_reserve_domain_model_order', ['uid' => 1])
            ->fetchAllAssociative();
        self::assertEquals([], $orders, 'No more order with uid 1 in database after command has been executed.');
    }

    /**
     * @test
     */
    public function commandRemovesReservationsRelatedToOrderThatWasRelatedToPeriod(): void
    {
        // Create an instance of the command and inject dependencies
        $command = GeneralUtility::makeInstance(RemovePastPeriodsCommand::class);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $orders = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_reserve_domain_model_reservation')
            ->select(['*'], 'tx_reserve_domain_model_reservation', ['customer_order' => 1])
            ->fetchAllAssociative();
        self::assertEquals(
            [],
            $orders,
            'No more reservations related to the order that was related to the past ' .
            'period in database after command has been executed.'
        );
    }
}
