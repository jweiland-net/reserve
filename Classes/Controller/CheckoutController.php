<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace JWeiland\Reserve\Controller;

use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Domain\Model\Period;
use JWeiland\Reserve\Domain\Repository\OrderRepository;
use JWeiland\Reserve\Domain\Repository\PeriodRepository;
use JWeiland\Reserve\Service\CheckoutService;
use JWeiland\Reserve\Utility\CacheUtility;
use JWeiland\Reserve\Utility\OrderSessionUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

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
        $this->view->assign('periods', $this->periodRepository->findUpcomingAndRunningByFacility((int)$this->settings['facility']));
        $this->view->assign('isBookingAllowed', OrderSessionUtility::isUserAllowedToOrder((int)$this->settings['facility']));
        CacheUtility::addFacilityToCurrentPageCacheTags((int)$this->settings['facility']);
    }

    /**
     * @param \JWeiland\Reserve\Domain\Model\Period $period
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function formAction(Period $period)
    {
        if (!OrderSessionUtility::isUserAllowedToOrder($period->getFacility()->getUid())) {
            // no need for a flashMessage because of {isBookingAllowed} inside the listAction fluid template
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
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function createAction(Order $order, int $amountOfPeople)
    {
        if (!$order->_isNew() || !OrderSessionUtility::isUserAllowedToOrder($order->getBookedPeriod()->getFacility()->getUid())) {
            $this->addFlashMessage('You are not allowed to order right now.','', AbstractMessage::ERROR);
            return $this->redirect('list');
        }
        if ($this->checkoutService->checkout($order, $amountOfPeople)) {
            $this->checkoutService->sendConfirmationMail($order);
        } else {
            $this->addFlashMessage(
                'Could not create an order for your selected amount of people',
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
            } else {
                $this->checkoutService->confirm($order);
                $this->view->assign('order', $order);
            }
        } else {
            $this->addFlashMessage(
                'Could not find any order with current combination of email and activation code.',
                '',
                AbstractMessage::ERROR
            );
        }
    }
}
