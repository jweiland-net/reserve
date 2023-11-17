<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Event;

use JWeiland\Reserve\Domain\Model\Order;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class ValidateOrderEvent
{
    private Order $order;

    private ObjectStorage $errorResults;

    public function __construct(Order $order, ObjectStorage $errorResults)
    {
        $this->order = $order;
        $this->errorResults = $errorResults;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getErrorResults(): ObjectStorage
    {
        return $this->errorResults;
    }
}
