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
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validator to validate a new order
 */
class OrderValidator extends AbstractValidator
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(Dispatcher $dispatcher, array $options = [])
    {
        parent::__construct($options);
        $this->dispatcher = $dispatcher;
    }

    protected function isValid($order): void
    {
        if (!$order instanceof Order) {
            $this->addError('The given object is not an order!', 1590479923299);
            return;
        }
        if (!GeneralUtility::validEmail($order->getEmail())) {
            $this->addError('The selected email is not valid!', 1590480086004);
        }

        $this->attachForeignResults($order);
    }

    protected function attachForeignResults(Order $order): void
    {
        $results = new ObjectStorage();
        // TODO: Add event thats replaces the deprecated signal slot
        $this->dispatcher->dispatch(__CLASS__, 'validateOrder', [
            'order' => $order,
            'errorResults' => $results,
        ]);
        foreach ($results as $result) {
            $this->result->merge($result);
        }
    }
}
