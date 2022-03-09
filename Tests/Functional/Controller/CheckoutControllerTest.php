<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tests\Functional\Controller;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;

class CheckoutControllerTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/reserve'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/../Fixtures/example_facility_with_period.xml');
    }

    /**
     * @test
     */
    public function formActionRendersFuturePeriod()
    {
        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_reserve_domain_model_period')
            ->update(
                'tx_reserve_domain_model_period',
                ['date' => (new \DateTime('+2 days midnight'))->getTimestamp()],
                ['uid' => 1]
            );

    }
}
