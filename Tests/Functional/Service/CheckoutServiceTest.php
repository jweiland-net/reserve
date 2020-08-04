<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tests\Functional\Service;

use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Domain\Repository\OrderRepository;
use JWeiland\Reserve\Domain\Repository\PeriodRepository;
use JWeiland\Reserve\Service\CheckoutService;
use JWeiland\Reserve\Service\MailService;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Prophecy\Argument;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class CheckoutServiceTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/reserve'];

    /**
     * @var CheckoutService
     */
    protected $checkoutService;

    protected function setUp()
    {
        parent::setUp();
        $GLOBALS['TYPO3_REQUEST'] = new ServerRequest('https://example.tld');
        if (class_exists(Site::class)) {
            // TYPO3 >= 9
            $GLOBALS['TSFE'] = new TypoScriptFrontendController(null, new Site('test', 1, []), new SiteLanguage(1, 'en_US', new Uri('https://example.tld'), []));
        } else {
            // TYPO3 8
            $GLOBALS['TSFE'] = new TypoScriptFrontendController(null, 1, 1);
        }

        $GLOBALS['TSFE']->fe_user = new FrontendUserAuthentication();
        $GLOBALS['TSFE']->id = 1;

        if (class_exists(LanguageService::class)) {
            // TYPO3 >= 10
            $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageService::class);
        } else {
            // TYPO3 < 10
            $GLOBALS['LANG'] = GeneralUtility::makeInstance(\TYPO3\CMS\Lang\LanguageService::class);
        }
        $GLOBALS['LANG']->init('default');

        $this->checkoutService = GeneralUtility::makeInstance(ObjectManager::class)->get(CheckoutService::class);

        $this->importDataSet(__DIR__ . '/../Fixtures/example_facility_with_period.xml');
    }

    /**
     * @test
     */
    public function checkoutPersistsNewOrderIntoDatabase()
    {
        $periodRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(PeriodRepository::class);
        $period = $periodRepository->findByUid(1);

        $order = new Order();
        $order->setFirstName('John');
        $order->setLastName('Doe');
        $order->setEmail('john.doe@domain.tld');
        $order->setBookedPeriod($period);

        $this->checkoutService->checkout($order, 1);

        self::assertEquals(1, $order->getUid(), 'Order UID changes to 1 after checkout');
    }

    /**
     * @test
     */
    public function sendConfirmationMailSendsMailWithActivationLinks()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/non_activated_order_with_reservations.xml');

        $orderRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(OrderRepository::class);
        $order = $orderRepository->findByUid(1);

        $mailService = $this->prophesize(MailService::class);

        $mailService->sendMailToCustomer(Argument::cetera())->willReturn(false);
        $mailService
            ->sendMailToCustomer(
                $order,
                'Test confirmation',
                Argument::containingString('Confirm your reservation'),
                Argument::cetera()
            )
            ->shouldBeCalled()
            ->willReturn(true);

        GeneralUtility::setSingletonInstance(MailService::class, $mailService->reveal());

        $this->checkoutService->sendConfirmationMail($order);

        GeneralUtility::removeSingletonInstance(MailService::class, $mailService->reveal());
    }

    /**
     * @test
     */
    public function confirmActivatesOrderAndSendsReservationMail()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/non_activated_order_with_reservations.xml');

        $orderRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(OrderRepository::class);
        $order = $orderRepository->findByUid(1);

        $mailService = $this->prophesize(MailService::class);
        $mailService->sendMailToCustomer(Argument::cetera())->willReturn(false);
        $mailService
            ->sendMailToCustomer(
                $order,
                'Test reservation',
                Argument::containingString('alt="firstCode"'), // reservation code
                Argument::cetera()
            )
            ->shouldBeCalled()
            ->willReturn(true);

        GeneralUtility::setSingletonInstance(MailService::class, $mailService->reveal());

        $this->checkoutService->confirm($order);

        self::assertTrue($order->isActivated(), 'Order is activated after CheckoutService::confirm');

        GeneralUtility::removeSingletonInstance(MailService::class, $mailService->reveal());
    }
}
