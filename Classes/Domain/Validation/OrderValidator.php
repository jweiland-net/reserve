<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Domain\Validation;

use JWeiland\Reserve\Domain\Model\Order;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validator to validate a new order
 */
class OrderValidator extends AbstractValidator
{
    protected function isValid($order)
    {
        if (!$order instanceof Order) {
            $this->addError('The given object is not an order!', 1590479923299);
            return;
        }
        if (!$order->getBookedPeriod()->isBookable()) {
            $this->addError('The selected period can not be booked at the moment!', 1589379319);
        }
        if (!GeneralUtility::validEmail($order->getEmail())) {
            $this->addError('The selected email is not valid!', 1590480086004);
        }
    }
}
