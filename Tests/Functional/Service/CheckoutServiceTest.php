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
use JWeiland\Reserve\Domain\Model\Participant;
use JWeiland\Reserve\Domain\Repository\OrderRepository;
use JWeiland\Reserve\Domain\Repository\PeriodRepository;
use JWeiland\Reserve\Domain\Repository\ReservationRepository;
use JWeiland\Reserve\Service\CheckoutService;
use JWeiland\Reserve\Service\MailService;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Prophecy\Argument;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class CheckoutServiceTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/reserve'];

    /**
     * @var CheckoutService
     */
    protected $checkoutService;

    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['TYPO3_REQUEST'] = new ServerRequest('https://example.tld');
        $GLOBALS['TSFE'] = new TypoScriptFrontendController(null, new Site('test', 1, []), new SiteLanguage(1, 'en_US', new Uri('https://example.tld'), []));
        $GLOBALS['TSFE']->fe_user = new FrontendUserAuthentication();
        $GLOBALS['TSFE']->id = 1;
        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageService::class);
        $GLOBALS['LANG']->init('default');

        $this->checkoutService = GeneralUtility::makeInstance(ObjectManager::class)->get(CheckoutService::class);

        $this->importDataSet(__DIR__ . '/../Fixtures/example_facility_with_period.xml');

        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_reserve_domain_model_period')
            ->update(
                'tx_reserve_domain_model_period',
                ['date' => (new \DateTime('+2 days midnight'))->getTimestamp()],
                ['uid' => 1]
            );
    }

    protected function tearDown(): void
    {
        unset($this->checkoutService);
    }

    /**
     * @test
     */
    public function checkoutPersistsNewOrderIntoDatabase()
    {
        $periodRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(PeriodRepository::class);
        $period = $periodRepository->findByUid(1);
        $participants = new ObjectStorage();
        $participant1 = new Participant();
        $participant1->setFirstName('First Name');
        $participant1->setLastName('Last Name');
        $participants->attach($participant1);

        $order = new Order();
        $order->setFirstName('John');
        $order->setLastName('Doe');
        $order->setEmail('john.doe@domain.tld');
        $order->setBookedPeriod($period);
        $order->setParticipants($participants);

        $this->checkoutService->checkout($order);

        self::assertEquals(1, $order->getUid(), 'Order UID changes to 1 after checkout');
    }

    /**
     * @test
     */
    public function checkoutPersistsMultipleReservationsIntoDatabase()
    {
        $periodRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(PeriodRepository::class);
        $period = $periodRepository->findByUid(1);
        $participants = new ObjectStorage();
        $participant1 = new Participant();
        $participant1->setFirstName('First Name');
        $participant1->setLastName('Last Name');
        $participant2 = new Participant();
        $participant2->setFirstName('First Name2');
        $participant2->setLastName('Last Name2');
        $participants->attach($participant1);
        $participants->attach($participant2);

        $order = new Order();
        $order->setFirstName('John');
        $order->setLastName('Doe');
        $order->setEmail('john.doe@domain.tld');
        $order->setBookedPeriod($period);
        $order->setParticipants($participants);

        $this->checkoutService->checkout($order);

        $reservationRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(ReservationRepository::class);
        $reservations = $reservationRepository->findByCustomerOrder(1);

        self::assertCount(
            3,
            $reservations,
            'Database contains 3 reservations after checkout of reservation with 2 further participants'
        );
    }

    /**
     * @test
     */
    public function checkoutDoesNotPersistBecauseTooMuchParticipants()
    {
        $periodRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(PeriodRepository::class);
        $period = $periodRepository->findByUid(1);
        $participants = new ObjectStorage();
        $participant1 = new Participant();
        $participant1->setFirstName('First Name');
        $participant1->setLastName('Last Name');
        $participant2 = new Participant();
        $participant2->setFirstName('First Name2');
        $participant2->setLastName('Last Name2');
        $participant3 = new Participant();
        $participant3->setFirstName('First Name3');
        $participant3->setLastName('Last Name3');
        $participants->attach($participant1);
        $participants->attach($participant2);
        $participants->attach($participant3);

        $order = new Order();
        $order->setFirstName('John');
        $order->setLastName('Doe');
        $order->setEmail('john.doe@domain.tld');
        $order->setBookedPeriod($period);
        $order->setParticipants($participants);

        self::assertFalse(
            $this->checkoutService->checkout($order),
            'Checkout returns false and does not persist order because too much participants are requested.'
        );
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
