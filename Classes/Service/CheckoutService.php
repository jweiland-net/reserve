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

namespace JWeiland\Reserve\Service;

use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Domain\Model\Reservation;
use JWeiland\Reserve\Utility\CheckoutUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class CheckoutService
{
    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    public function __construct(PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * Create $amountOfReservations reservation records and add them to $order.
     * Set activation code for $order and all new reservations inside the current $order.
     * Use $order after $checkout to proceed
     *
     * @param Order $order
     * @param int $amountOfReservations
     * @return bool true on success, otherwise false
     */
    public function checkout(Order $order, int $amountOfReservations): bool
    {
        $success = true;
        if ($amountOfReservations > $order->getBookedPeriod()->getMaxParticipantsPerOrder()) {
            $success = false;
        } else {
            $order->setActivationCode(CheckoutUtility::generateActivationCodeForOrder());
            for ($i = 0; $i < $amountOfReservations; $i++) {
                /** @var Reservation $reservation */
                $reservation = GeneralUtility::makeInstance(Reservation::class);
                $reservation->setCustomerOrder($order);
                $reservation->setCode(CheckoutUtility::generateCodeForReservation());
                $order->getReservations()->attach($reservation);
                $this->persistenceManager->add($reservation);
            }
            $this->persistenceManager->add($order);
            $this->persistenceManager->persistAll();
        }
        return $success;
    }
}
