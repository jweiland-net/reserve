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
use JWeiland\Reserve\Event\ValidateOrderEvent;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validator to validate a new order
 */
class OrderValidator extends AbstractValidator
{
    protected function isValid($value): void
    {
        if (!$value instanceof Order) {
            $this->addError('The given object is not an order!', 1679403006);
            return;
        }

        if (!GeneralUtility::validEmail($value->getEmail())) {
            $this->addError('The selected email is not valid!', 1679403017);
        }

        $this->attachForeignResults($value);
    }

    protected function attachForeignResults(Order $order): void
    {
        $errorResults = new ObjectStorage();

        /** @var ValidateOrderEvent $event */
        $event = $this->getEventDispatcher()->dispatch(
            new ValidateOrderEvent($order, $errorResults),
        );

        foreach ($event->getErrorResults() as $errorResult) {
            $this->result->merge($errorResult);
        }
    }

    protected function getEventDispatcher(): EventDispatcher
    {
        return GeneralUtility::makeInstance(EventDispatcher::class);
    }
}
