<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Controller\v8;

use JWeiland\Reserve\Domain\Model\Order;

/**
 * CheckoutController replacement for TYPO3 v8 because of annotations
 *
 * @internal
 */
class CheckoutController extends \JWeiland\Reserve\Controller\CheckoutController
{
    /**
     * @param Order $order
     * @param int $amountOfPeople
     * @validate $order \JWeiland\Reserve\Domain\Validation\OrderValidator
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function createAction(Order $order, int $amountOfPeople = 1)
    {
        return parent::createAction($order, $amountOfPeople);
    }
}
