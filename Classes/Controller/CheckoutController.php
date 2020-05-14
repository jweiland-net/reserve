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
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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

    public function __construct(PeriodRepository $periodRepository, OrderRepository $orderRepository, CheckoutService $checkoutService)
    {
        $this->periodRepository = $periodRepository;
        $this->orderRepository = $orderRepository;
        $this->checkoutService = $checkoutService;
    }

    public function listAction()
    {
        $this->view->assign('periods', $this->periodRepository->findByFacility((int)$this->settings['facility']));
    }

    public function formAction(Period $period)
    {
        /** @var Order $order */
        $order = GeneralUtility::makeInstance(Order::class);
        $order->setBookedPeriod($period);
        $this->view->assign('order', $order);
    }

    public function createAction(Order $order, int $amountOfPeople)
    {
        if (!$order->_isNew()) {
            return;
        }
        if ($this->checkoutService->checkout($order, $amountOfPeople)) {
            $this->checkoutService->sendConfirmationMail($order);
        } else {
            $this->addFlashMessage(
                'Could not create an order for your selected amount of people',
                '',
                AbstractMessage::ERROR
            );
        }
    }

    public function confirmAction(string $email, string $activationCode)
    {
        $order = $this->orderRepository->findByEmailAndActivationCode($email, $activationCode);
        if ($order instanceof Order) {
            $this->checkoutService->confirm($order);
        } else {
            $this->addFlashMessage(
                'Could not find any order with current combination of email and activation code.',
                '',
                AbstractMessage::ERROR
            );
        }
    }
}
