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
    protected Order $customerOrder;

    protected string $firstName = '';

    protected string $lastName = '';

    protected string $code = '';

    protected bool $used = false;

    public function getCustomerOrder(): Order
    {
        return $this->customerOrder;
    }

    public function setCustomerOrder(Order $customerOrder): void
    {
        $this->customerOrder = $customerOrder;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

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

        return $period->getDate()->getTimestamp() === $currentDate
            && $period->getBegin()->getTimestamp() <= $currentTime
            && ($period->getEnd() === null || $period->getEnd()->getTimestamp() > $currentTime);
    }
}
