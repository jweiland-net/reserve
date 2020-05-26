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
    public function createAction(Order $order, int $amountOfPeople)
    {
        return parent::createAction($order, $amountOfPeople);
    }
}
