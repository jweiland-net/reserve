<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Controller;

use JWeiland\Reserve\Configuration\ExtConf;
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
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller to list and order reservable periods of a selected facility
 */
class CheckoutController extends ActionController
{
    public function __construct(
        protected readonly FacilityRepository $facilityRepository,
        protected readonly PeriodRepository $periodRepository,
        protected readonly OrderRepository $orderRepository,
        protected readonly CheckoutService $checkoutService,
        protected readonly DataTablesService $dataTablesService,
        protected readonly CancellationService $cancellationService,
        protected readonly ExtConf $extConf,
    ) {}

    public function listAction(): ResponseInterface
    {
        $facilities = $this->facilityRepository->findByUids(
            GeneralUtility::trimExplode(',', $this->settings['facility'])
        );
        $periods = $this->periodRepository->findUpcomingAndRunningByFacilityUids(
            GeneralUtility::trimExplode(',', $this->settings['facility'], true),
        );

        $this->view->assign('facilities', $facilities);
        $this->view->assign('periods', $periods);

        $orderColumnBegin = count($facilities) === 1 ? 0 : 1;
        $dataTablesConfiguration = $this->dataTablesService->getConfiguration();
        $additionalDefaultConfigurarion = $this->getAdditionalDefaultConfiguration($orderColumnBegin);
        $this->view->assign(
            'jsConf',
            [
                'datatables' => $dataTablesConfiguration + $additionalDefaultConfigurarion,
            ],
        );
        CacheUtility::addFacilityToCurrentPageCacheTags((int)$this->settings['facility'], $this->request);

        return $this->htmlResponse();
    }

    public function formAction(Period $period): ResponseInterface
    {
        if (!$period->isBookable()) {
            $this->redirect('list');
        }

        if (!OrderSessionUtility::isUserAllowedToOrder($period->getFacility()->getUid(), $this->request)) {
            $this->addFlashMessage(
                LocalizationUtility::translate('list.alerts.isBookingAllowed', 'reserve'),
                '',
                ContextualFeedbackSeverity::INFO,
            );
            $this->redirect('list');
        }

        /** @var Order $order */
        $order = GeneralUtility::makeInstance(Order::class);
        $order->setBookedPeriod($period);

        $this->view->assign('order', $order);

        return $this->htmlResponse();
    }

    /**
     * @Extbase\Validate("JWeiland\Reserve\Domain\Validation\OrderValidator", param="order")
     */
    public function createAction(Order $order, int $furtherParticipants = 0): ResponseInterface
    {
        if (!(
            $order->_isNew()
            && $order->getBookedPeriod()->isBookable()
            && OrderSessionUtility::isUserAllowedToOrder(
                $order->getBookedPeriod()->getFacility()->getUid(),
                $this->request,
            )
        )) {
            $this->addFlashMessage(
                'You are not allowed to order right now.',
                '',
                ContextualFeedbackSeverity::ERROR,
            );
            return $this->redirect('list');
        }

        if ($this->checkoutService->checkout($order, $this->request, (int)$this->settings['orderPid'], $furtherParticipants)) {
            $this->checkoutService->sendConfirmationMail($order);
            $this->addFlashMessage(LocalizationUtility::translate('reservation.created', 'reserve'));
            return $this->redirect('list');
        }

        $this->addFlashMessage(
            LocalizationUtility::translate('list.alerts.wrongAmountOfReservations', 'reserve'),
            '',
            ContextualFeedbackSeverity::ERROR,
        );

        return $this->redirect('form', null, null, ['period' => $order->getBookedPeriod()]);
    }

    public function confirmAction(string $email, string $activationCode): ResponseInterface
    {
        $this->view->assign('configurations', $this->extConf);
        $order = $this->orderRepository->findByEmailAndActivationCode($email, $activationCode);
        if ($order instanceof Order) {
            if ($order->isActivated()) {
                $this->addFlashMessage(
                    'Your order is already confirmed! Please check your mailbox.',
                    '',
                    ContextualFeedbackSeverity::INFO,
                );
                return $this->redirect('list');
            }

            $this->checkoutService->confirm($order);
            $this->view->assign('order', $order);
        } else {
            $this->addFlashMessage(
                'Could not find any order with current combination of email and activation code.',
                '',
                ContextualFeedbackSeverity::ERROR,
            );
        }

        return $this->htmlResponse();
    }

    public function cancelAction(string $email, string $activationCode, bool $confirm = false): ResponseInterface
    {
        $order = $this->orderRepository->findByEmailAndActivationCode($email, $activationCode);
        // Early return
        if (!$order instanceof Order) {
            $this->addFlashMessage(
                'Could not find any order with current combination of email and activation code.',
                '',
                ContextualFeedbackSeverity::ERROR,
            );
            return $this->redirect('list');
        }

        $redirect = true;
        if ($order->isCancelable()) {
            $this->view->assign('order', $order);
            if ($confirm) {
                try {
                    $this->cancellationService->cancel($order, $this->request);
                    $this->addFlashMessage(LocalizationUtility::translate('cancel.cancelled', 'reserve'));
                } catch (\Throwable $exception) {
                    $this->addFlashMessage(
                        'Could not cancel your order. Please contact the administrator!',
                        '',
                        ContextualFeedbackSeverity::ERROR,
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
                            $order->getCancelableUntil()->getTimestamp(),
                        ),
                    ],
                ),
                '',
                ContextualFeedbackSeverity::WARNING,
            );
        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'flashMessage.notCancelable',
                    'reserve',
                ),
                '',
                ContextualFeedbackSeverity::WARNING,
            );
        }

        if ($redirect) {
            CacheUtility::clearPageCachesForPagesWithCurrentFacility($order->getBookedPeriod()->getFacility()->getUid());
            return $this->redirect('list');
        }

        return $this->htmlResponse();
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
