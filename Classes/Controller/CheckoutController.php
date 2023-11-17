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
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller to list and order reservable periods of a selected facility
 */
class CheckoutController extends ActionController
{
    protected FacilityRepository $facilityRepository;

    protected PeriodRepository $periodRepository;

    protected OrderRepository $orderRepository;

    protected CheckoutService $checkoutService;

    protected DataTablesService $dataTablesService;

    protected CancellationService $cancellationService;

    public function injectFacilityRepository(FacilityRepository $facilityRepository): void
    {
        $this->facilityRepository = $facilityRepository;
    }

    public function injectPeriodRepository(PeriodRepository $periodRepository): void
    {
        $this->periodRepository = $periodRepository;
    }

    public function injectOrderRepository(OrderRepository $orderRepository): void
    {
        $this->orderRepository = $orderRepository;
    }

    public function injectCheckoutService(CheckoutService $checkoutService): void
    {
        $this->checkoutService = $checkoutService;
    }

    public function injectDataTablesService(DataTablesService $dataTablesService): void
    {
        $this->dataTablesService = $dataTablesService;
    }

    public function injectCancellationService(CancellationService $cancellationService): void
    {
        $this->cancellationService = $cancellationService;
    }

    public function listAction(): void
    {
        $facilities = $this->facilityRepository->findByUids(GeneralUtility::trimExplode(',', $this->settings['facility']));
        $this->view->assign('facilities', $facilities);
        $this->view->assign('periods', $this->periodRepository->findUpcomingAndRunningByFacilityUids(GeneralUtility::trimExplode(',', $this->settings['facility'])));
        $orderColumnBegin = count($facilities) === 1 ? 0 : 1;
        $dataTablesConfiguration = $this->dataTablesService->getConfiguration();
        $additionalDefaultConfigurarion = $this->getAdditionalDefaultConfiguration($orderColumnBegin);
        $this->view->assign(
            'jsConf',
            [
                'datatables' => $dataTablesConfiguration + $additionalDefaultConfigurarion,
            ]
        );
        CacheUtility::addFacilityToCurrentPageCacheTags((int)$this->settings['facility']);
    }

    public function formAction(Period $period): void
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
     * @Extbase\Validate("JWeiland\Reserve\Domain\Validation\OrderValidator", param="order")
     */
    public function createAction(Order $order, int $furtherParticipants = 0): void
    {
        if (!(
            $order->_isNew()
            && $order->getBookedPeriod()->isBookable()
            && OrderSessionUtility::isUserAllowedToOrder($order->getBookedPeriod()->getFacility()->getUid())
        )) {
            $this->addFlashMessage(
                'You are not allowed to order right now.',
                '',
                AbstractMessage::ERROR
            );
            $this->redirect('list');
        }

        if ($this->checkoutService->checkout($order, (int)$this->settings['orderPid'], $furtherParticipants)) {
            $this->checkoutService->sendConfirmationMail($order);
            $this->addFlashMessage(LocalizationUtility::translate('reservation.created', 'reserve'));
            $this->redirect('list');
        }

        $this->addFlashMessage(
            LocalizationUtility::translate('list.alerts.wrongAmountOfReservations', 'reserve'),
            '',
            AbstractMessage::ERROR
        );

        $this->redirect('form', null, null, ['period' => $order->getBookedPeriod()]);
    }

    public function confirmAction(string $email, string $activationCode): void
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

    public function cancelAction(string $email, string $activationCode, bool $confirm = false): void
    {
        $order = $this->orderRepository->findByEmailAndActivationCode($email, $activationCode);

        // Early return
        if (!$order instanceof Order) {
            $this->addFlashMessage(
                'Could not find any order with current combination of email and activation code.',
                '',
                AbstractMessage::ERROR
            );
            $this->redirect('list');
        }

        $redirect = true;
        if ($order->isCancelable()) {
            $this->view->assign('order', $order);
            if ($confirm) {
                try {
                    $this->cancellationService->cancel($order);
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
                        ),
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
            $this->redirect('list');
        }
    }

    private function getAdditionalDefaultConfiguration(int $orderColumnBegin): array
    {
        return [
            'searching' => false,
            'columnDefs' => [
                [
                    'targets' => 4,
                    'orderable' => false,
                ],
            ],
            'order' => [
                [
                    $orderColumnBegin,
                    'asc',
                ],
                [
                    $orderColumnBegin + 1,
                    'asc',
                ],
            ],
        ];
    }
}
