<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Controller;

use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Domain\Model\Period;
use JWeiland\Reserve\Domain\Repository\OrderRepository;
use JWeiland\Reserve\Domain\Repository\PeriodRepository;
use JWeiland\Reserve\Service\CancellationService;
use JWeiland\Reserve\Service\CheckoutService;
use JWeiland\Reserve\Service\DataTablesService;
use JWeiland\Reserve\Utility\CacheUtility;
use JWeiland\Reserve\Utility\OrderSessionUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller to list and order reservable periods of a selected facility
 */
class CheckoutController extends ActionController
{
    /**
     * @var PeriodRepository
     */
    protected $periodRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var CheckoutService
     */
    protected $checkoutService;

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @param \JWeiland\Reserve\Domain\Repository\PeriodRepository $periodRepository
     * @param \JWeiland\Reserve\Domain\Repository\OrderRepository $orderRepository
     * @param \JWeiland\Reserve\Service\CheckoutService $checkoutService
     */
    public function __construct(PeriodRepository $periodRepository, OrderRepository $orderRepository, CheckoutService $checkoutService)
    {
        $this->periodRepository = $periodRepository;
        $this->orderRepository = $orderRepository;
        $this->checkoutService = $checkoutService;
    }

    public function listAction()
    {
        $this->view->assign('jsConf', [
            'datatables' => GeneralUtility::makeInstance(DataTablesService::class)->getConfiguration()
                + ['searching' => false, 'columnDefs' => [['targets' => 4, 'orderable' => false]]]
        ]);
        $this->view->assign('periods', $this->periodRepository->findUpcomingAndRunningByFacility((int)$this->settings['facility']));
        CacheUtility::addFacilityToCurrentPageCacheTags((int)$this->settings['facility']);
    }

    /**
     * @param \JWeiland\Reserve\Domain\Model\Period $period
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function formAction(Period $period)
    {
        if (!OrderSessionUtility::isUserAllowedToOrder($period->getFacility()->getUid())) {
            $this->addFlashMessage(
                LocalizationUtility::translate('list.alerts.isBookingAllowed', 'reserve'),
                '',
                AbstractMessage::INFO
            );
            return $this->redirect('list');
        }
        /** @var Order $order */
        $order = GeneralUtility::makeInstance(Order::class);
        $order->setBookedPeriod($period);
        $this->view->assign('order', $order);
    }

    /**
     * @param \JWeiland\Reserve\Domain\Model\Order $order
     * @param int $amountOfPeople
     * @TYPO3\CMS\Extbase\Annotation\Validate("JWeiland\Reserve\Domain\Validation\OrderValidator", param="order")
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function createAction(Order $order, int $amountOfPeople = 1)
    {
        if (!$order->_isNew() || !OrderSessionUtility::isUserAllowedToOrder($order->getBookedPeriod()->getFacility()->getUid())) {
            $this->addFlashMessage('You are not allowed to order right now.', '', AbstractMessage::ERROR);
            return $this->redirect('list');
        }
        if ($this->checkoutService->checkout($order, $amountOfPeople, (int)$this->settings['orderPid'])) {
            $this->checkoutService->sendConfirmationMail($order);
        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('list.alerts.wrongAmountOfReservations', 'reserve'),
                '',
                AbstractMessage::ERROR
            );
            return $this->redirect('form', null, null, ['period' => $order->getBookedPeriod()]);
        }
    }

    /**
     * @param string $email
     * @param string $activationCode
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function confirmAction(string $email, string $activationCode)
    {
        $order = $this->orderRepository->findByEmailAndActivationCode($email, $activationCode);
        if ($order instanceof Order) {
            if ($order->isActivated()) {
                $this->addFlashMessage(
                    'Your order is already confirmed! Please check your mailbox.',
                    '',
                    AbstractMessage::INFO
                );
                return $this->redirect('list');
            }
            $this->checkoutService->confirm($order);
            $this->view->assign('order', $order);
        } else {
            $this->addFlashMessage(
                'Could not find any order with current combination of email and activation code.',
                '',
                AbstractMessage::ERROR
            );
        }
    }

    /**
     * @param string $email
     * @param string $activationCode
     * @param bool $confirm
     */
    public function cancelAction(string $email, string $activationCode, bool $confirm = false)
    {
        $order = $this->orderRepository->findByEmailAndActivationCode($email, $activationCode);
        if ($order instanceof Order) {
            $redirect = true;
            if ($order->isCancelable()) {
                $this->view->assign('order', $order);
                if ($confirm) {
                    $cancellationService = $this->objectManager->get(CancellationService::class);
                    try {
                        $cancellationService->cancel($order);
                        $this->addFlashMessage(LocalizationUtility::translate('cancel.cancelled', 'reserve'));
                    } catch (\Throwable $exception) {
                        $this->addFlashMessage(
                            'Could not cancel your order. Please contact the administrator!',
                            '',
                            AbstractMessage::ERROR
                        );
                    }
                } else {
                    $redirect = false;
                }
            } elseif ($order->isActivated() && $order->getBookedPeriod()->getFacility()->isCancelable()) {
                $this->addFlashMessage(
                    LocalizationUtility::translate(
                        'flashMessage.noLongerCancelable',
                        'reserve',
                        [
                            strftime(
                                LocalizationUtility::translate('date_format_full', 'reserve'),
                                $order->getCancelableUntil()->getTimestamp()
                            )
                        ]
                    ),
                    '',
                    AbstractMessage::WARNING
                );
            }
            if ($redirect) {
                CacheUtility::clearPageCachesForPagesWithCurrentFacility($order->getBookedPeriod()->getFacility()->getUid());
                return $this->redirect('list');
            }
        } else {
            $this->addFlashMessage(
                'Could not find any order with current combination of email and activation code.',
                '',
                AbstractMessage::ERROR
            );
            return $this->redirect('list');
        }
    }
}
