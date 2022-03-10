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
use JWeiland\Reserve\Domain\Repository\FacilityRepository;
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
     * @var FacilityRepository
     */
    protected $facilityRepository;

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
     * @param FacilityRepository $facilityRepository
     * @param PeriodRepository $periodRepository
     * @param OrderRepository $orderRepository
     * @param CheckoutService $checkoutService
     */
    public function __construct(FacilityRepository $facilityRepository, PeriodRepository $periodRepository, OrderRepository $orderRepository, CheckoutService $checkoutService)
    {
        $this->facilityRepository = $facilityRepository;
        $this->periodRepository = $periodRepository;
        $this->orderRepository = $orderRepository;
        $this->checkoutService = $checkoutService;
    }

    public function listAction(): void
    {
        // Uncached list action used for redirects with flash messages as they are cached otherwise!
        // See: https://forge.typo3.org/issues/72703
        if ($this->controllerContext->getFlashMessageQueue()->count()) {
            $GLOBALS['TSFE']->no_cache = true;
        }

        $this->view->assign('jsConf', [
            'datatables' => GeneralUtility::makeInstance(DataTablesService::class)->getConfiguration()
                + ['searching' => false, 'columnDefs' => [['targets' => 4, 'orderable' => false]], 'order' => [[1, 'asc']]]
        ]);
        $facilities = $this->facilityRepository->findByUids(GeneralUtility::trimExplode(',', $this->settings['facility']));
        $this->view->assign('facilities', $facilities);
        $this->view->assign('periods', $this->periodRepository->findUpcomingAndRunningByFacilityUids(GeneralUtility::trimExplode(',', $this->settings['facility'])));
        CacheUtility::addFacilityToCurrentPageCacheTags((int)$this->settings['facility']);
    }

    /**
     * @param \JWeiland\Reserve\Domain\Model\Period $period
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function formAction(Period $period)
    {
        if (!$period->isBookable()) {
            $this->redirect('list');
        }
        if (!OrderSessionUtility::isUserAllowedToOrder($period->getFacility()->getUid())) {
            $this->addFlashMessage(
                LocalizationUtility::translate('list.alerts.isBookingAllowed', 'reserve'),
                '',
                AbstractMessage::INFO
            );
            $this->redirect('list');
        }
        /** @var Order $order */
        $order = GeneralUtility::makeInstance(Order::class);
        $order->setBookedPeriod($period);
        $this->view->assign('order', $order);
    }

    /**
     * @param \JWeiland\Reserve\Domain\Model\Order $order
     * @param int $furtherParticipants
     * @TYPO3\CMS\Extbase\Annotation\Validate("JWeiland\Reserve\Domain\Validation\OrderValidator", param="order")
     */
    public function createAction(Order $order, int $furtherParticipants = 0)
    {
        if (!(
            $order->_isNew()
            && $order->getBookedPeriod()->isBookable()
            && OrderSessionUtility::isUserAllowedToOrder($order->getBookedPeriod()->getFacility()->getUid())
        )) {
            $this->addFlashMessage('You are not allowed to order right now.', '', AbstractMessage::ERROR);
            $this->redirect('list');
        }
        if ($this->checkoutService->checkout($order, (int)$this->settings['orderPid'], $furtherParticipants)) {
            $this->checkoutService->sendConfirmationMail($order);
            $this->addFlashMessage(LocalizationUtility::translate('reservation.created', 'reserve'));
            $this->redirect('list');
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
                $this->redirect('list');
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
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate(
                        'flashMessage.notCancelable',
                        'reserve'
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
