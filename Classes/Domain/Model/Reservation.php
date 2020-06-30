<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Reservation
 */
class Reservation extends AbstractEntity
{
    /**
     * @var \JWeiland\Reserve\Domain\Model\Order
     */
    protected $customerOrder;

    /**
     * @var string
     */
    protected $code = '';

    /**
     * @var bool
     */
    protected $used = false;

    /**
     * @return Order
     */
    public function getCustomerOrder(): Order
    {
        return $this->customerOrder;
    }

    /**
     * @param Order $customerOrder
     */
    public function setCustomerOrder(Order $customerOrder)
    {
        $this->customerOrder = $customerOrder;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * @return bool
     */
    public function isUsed(): bool
    {
        return $this->used;
    }

    /**
     * @param bool $used
     */
    public function setUsed(bool $used): void
    {
        $this->used = $used;
    }

    public function getIsCurrentlyValid(): bool
    {
        $period = $this->getCustomerOrder()->getBookedPeriod();
        $currentDate = strtotime('today');
        $currentDateTime = time();
        $currentTime = $currentDateTime - $currentDate;

        if (
            $period->getDate()->getTimestamp() === $currentDate
            && $period->getBegin()->getTimestamp() <= $currentTime
            && $period->getEnd()->getTimestamp() > $currentTime
        ) {
            return true;
        }

        return false;
    }
}
