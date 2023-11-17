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
use JWeiland\Reserve\Domain\Model\Period;
use JWeiland\Reserve\Domain\Repository\OrderRepository;
use JWeiland\Reserve\Domain\Repository\PeriodRepository;
use JWeiland\Reserve\Domain\Repository\ReservationRepository;
use JWeiland\Reserve\Service\CheckoutService;
use JWeiland\Reserve\Service\FluidService;
use JWeiland\Reserve\Service\MailService;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Authentication\CommandLineUserAuthentication;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class CheckoutServiceTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'jweiland/reserve',
    ];

    protected CheckoutService $subject;

    /**
     * @var FluidService|MockObject
     */
    protected $fluidServiceMock;

    /**
     * @var MailService|MockObject
     */
    protected $mailServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['TSFE'] = $this->getAccessibleMock(TypoScriptFrontendController::class, null, [], '', false);
        $GLOBALS['TSFE']->_set('site', new Site('test', 1, []));
        $GLOBALS['TSFE']->_set('sys_page', new PageRepository((new Context())));
        $GLOBALS['TSFE']->fe_user = new FrontendUserAuthentication();
        if (method_exists($GLOBALS['TSFE']->fe_user, 'initializeUserSessionManager')) {
            $GLOBALS['TSFE']->fe_user->initializeUserSessionManager();
        }
        $GLOBALS['TSFE']->id = 1;

        Bootstrap::initializeBackendUser(CommandLineUserAuthentication::class);
        Bootstrap::initializeLanguageObject();

        $this->fluidServiceMock = $this->createMock(FluidService::class);
        $this->mailServiceMock = $this->createMock(MailService::class);

        $this->subject = new CheckoutService(
            GeneralUtility::makeInstance(ConfigurationManagerInterface::class),
            $this->fluidServiceMock,
            $this->mailServiceMock,
            GeneralUtility::makeInstance(PersistenceManagerInterface::class)
        );

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/example_facility_with_period.csv');

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
        unset($this->subject);
    }

    /**
     * @test
     */
    public function checkoutPersistsNewOrderIntoDatabase(): void
    {
        $periodRepository = GeneralUtility::makeInstance(PeriodRepository::class);
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

        $this->subject->checkout($order);

        self::assertEquals(1, $order->getUid(), 'Order UID changes to 1 after checkout');
    }

    /**
     * @test
     */
    public function checkoutPersistsMultipleReservationsIntoDatabase(): void
    {
        $periodRepository = GeneralUtility::makeInstance(PeriodRepository::class);
        /** @var Period $period */
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

        $this->subject->checkout($order);

        $reservationRepository = GeneralUtility::makeInstance(ReservationRepository::class);
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
    public function checkoutDoesNotPersistBecauseTooMuchParticipants(): void
    {
        $periodRepository = GeneralUtility::makeInstance(PeriodRepository::class);
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
            $this->subject->checkout($order),
            'Checkout returns false and does not persist order because too much participants are requested.'
        );
    }

    /**
     * @test
     */
    public function sendConfirmationMailSendsMailWithActivationLinks(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/non_activated_order_with_reservations.csv');

        $orderRepository = GeneralUtility::makeInstance(OrderRepository::class);
        $order = $orderRepository->findByUid(1);

        $this->fluidServiceMock
            ->expects(self::atLeastOnce())
            ->method('replaceMarkerByRenderedTemplate')
            ->with(
                self::equalTo('###ORDER_DETAILS###'),
                self::equalTo('Confirmation'),
                self::equalTo($order->getBookedPeriod()->getFacility()->getConfirmationMailHtml()),
                [
                    'pageUid' => $GLOBALS['TSFE']->id,
                    'order' => $order,
                ]
            )
            ->willReturn('Confirm your reservation');

        $this->mailServiceMock
            ->expects(self::atLeastOnce())
            ->method('sendMailToCustomer')
            ->willReturnCallback(static function (Order $order, string $subject, string $body, ...$others) {
                return $subject === 'Test confirmation'
                    && $body === 'Confirm your reservation';
            });

        $this->subject->sendConfirmationMail($order);
    }

    /**
     * @test
     */
    public function confirmActivatesOrderAndSendsReservationMail(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/non_activated_order_with_reservations.csv');

        $orderRepository = GeneralUtility::makeInstance(OrderRepository::class);
        $order = $orderRepository->findByUid(1);

        $this->fluidServiceMock
            ->expects(self::atLeastOnce())
            ->method('replaceMarkerByRenderedTemplate')
            ->with(
                self::equalTo('###RESERVATION###'),
                self::equalTo('Reservation'),
                self::equalTo($order->getBookedPeriod()->getFacility()->getReservationMailHtml()),
                [
                    'pageUid' => $GLOBALS['TSFE']->id,
                    'order' => $order,
                ]
            )
            ->willReturn('alt="firstCode"');

        $this->mailServiceMock
            ->expects(self::atLeastOnce())
            ->method('sendMailToCustomer')
            ->willReturnCallback(static function (Order $order, string $subject, string $body, ...$others) {
                return $subject === 'Test reservation'
                    && str_contains($body, 'alt="firstCode"');
            });

        $this->subject->confirm($order);

        self::assertTrue($order->isActivated(), 'Order is activated after CheckoutService::confirm');
    }
}
