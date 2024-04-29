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
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Email extends AbstractEntity
{
    public const RECEIVER_TYPE_PERIODS = 0;
    public const RECEIVER_TYPE_MANUAL = 1;

    protected string $subject = '';

    protected string $body = '';

    /**
     * Use RECEIVER_TYPE_ constants!
     */
    protected int $receiverType = self::RECEIVER_TYPE_PERIODS;

    protected string $fromName = '';

    protected string $fromEmail = '';

    protected string $replyToName = '';

    protected string $replyToEmail = '';

    protected string $customReceivers = '';

    /**
     * @var ObjectStorage<Period>
     */
    protected ObjectStorage $periods;

    protected bool $locked = false;

    /**
     * Serialized JSON!
     *
     * @internal
     */
    protected string $commandData = '';

    /**
     * Unserialized object
     *
     * @internal
     */
    protected array $commandDataUnserialized = [];

    public function __construct()
    {
        $this->periods = new ObjectStorage();
    }

    /**
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject(): void
    {
        $this->periods = $this->periods ?? new ObjectStorage();
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getReceiverType(): int
    {
        return $this->receiverType;
    }

    /**
     * @param int $receiverType use RECEIVER_TYPE constants!
     */
    public function setReceiverType(int $receiverType): void
    {
        $this->receiverType = $receiverType;
    }

    public function getFromName(): string
    {
        return $this->fromName;
    }

    public function setFromName(string $fromName): void
    {
        $this->fromName = $fromName;
    }

    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    public function setFromEmail(string $fromEmail): void
    {
        $this->fromEmail = $fromEmail;
    }

    public function getReplyToName(): string
    {
        return $this->replyToName;
    }

    public function setReplyToName(string $replyToName): void
    {
        $this->replyToName = $replyToName;
    }

    public function getReplyToEmail(): string
    {
        return $this->replyToEmail;
    }

    public function setReplyToEmail(string $replyToEmail): void
    {
        $this->replyToEmail = $replyToEmail;
    }

    public function getCustomReceivers(): string
    {
        return $this->customReceivers;
    }

    public function setCustomReceivers(string $customReceivers): void
    {
        $this->customReceivers = $customReceivers;
    }

    /**
     * @return ObjectStorage|Period[]
     */
    public function getPeriods(): ObjectStorage
    {
        return $this->periods;
    }

    public function setPeriods(ObjectStorage $periods): void
    {
        $this->periods = $periods;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): void
    {
        $this->locked = $locked;
    }

    /**
     * @internal
     */
    public function getCommandData(): array
    {
        if (!$this->commandDataUnserialized) {
            $this->commandDataUnserialized = (array)unserialize($this->commandData, ['allowed_classes' => false]);
        }

        return $this->commandDataUnserialized;
    }

    /**
     * @internal
     */
    public function setCommandData(array $commandData): void
    {
        $this->commandData = serialize($commandData);
        $this->commandDataUnserialized = [];
    }

    /**
     * Get all receivers of this E-Mail instance using this method. Works
     * with all receiver types!
     *
     * @param array|null $orders reference if you need the associated orders if type is RECEIVER_TYPE_PERIODS
     */
    public function getReceivers(array &$orders = null): array
    {
        if ($this->receiverType === self::RECEIVER_TYPE_PERIODS) {
            $receivers = $this->getReceiversTypePeriods($orders);
        } else {
            $receivers = explode(',', $this->getCustomReceivers());
        }

        return $receivers;
    }

    protected function getReceiversTypePeriods(array &$orders = null): array
    {
        $orders = [];
        $emails = [];
        foreach ($this->getPeriods() as $period) {
            foreach ($period->getOrders() as $order) {
                if (in_array($order->getUid(), $this->getCommandData(), true)) {
                    // already processed
                    continue;
                }

                $emails[$order->getUid()] = $order->getEmail();
                $orders[$order->getUid()] = $order;
            }
        }

        return $emails;
    }
}
