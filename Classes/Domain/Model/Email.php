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
    const RECEIVER_TYPE_PERIODS = 0;
    const RECEIVER_TYPE_MANUAL = 1;

    /**
     * @var string
     */
    protected $subject = '';

    /**
     * @var string
     */
    protected $body = '';

    /**
     * @var int use RECEIVER_TYPE_ constants!
     */
    protected $receiverType = self::RECEIVER_TYPE_PERIODS;

    /**
     * @var string
     */
    protected $fromName = '';

    /**
     * @var string
     */
    protected $fromEmail = '';

    /**
     * @var string
     */
    protected $replyToName = '';

    /**
     * @var string
     */
    protected $replyToEmail = '';

    /**
     * @var string
     */
    protected $customReceivers = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Reserve\Domain\Model\Period>
     */
    protected $periods;

    /**
     * @var bool
     */
    protected $locked = false;

    /**
     * @var string serialized json!
     * @internal
     */
    protected $commandData = '';

    /**
     * @var array unserialized object
     * @internal
     */
    protected $commandDataUnserialized = [];

    public function __construct()
    {
        $this->periods = new ObjectStorage();
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body)
    {
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getReceiverType(): int
    {
        return $this->receiverType;
    }

    /**
     * @param int $receiverType use RECEIVER_TYPE constants!
     */
    public function setReceiverType(int $receiverType)
    {
        $this->receiverType = $receiverType;
    }

    /**
     * @return string
     */
    public function getFromName(): string
    {
        return $this->fromName;
    }

    /**
     * @param string $fromName
     */
    public function setFromName(string $fromName)
    {
        $this->fromName = $fromName;
    }

    /**
     * @return string
     */
    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    /**
     * @param string $fromEmail
     */
    public function setFromEmail(string $fromEmail)
    {
        $this->fromEmail = $fromEmail;
    }

    /**
     * @return string
     */
    public function getReplyToName(): string
    {
        return $this->replyToName;
    }

    /**
     * @param string $replyToName
     */
    public function setReplyToName(string $replyToName)
    {
        $this->replyToName = $replyToName;
    }

    /**
     * @return string
     */
    public function getReplyToEmail(): string
    {
        return $this->replyToEmail;
    }

    /**
     * @param string $replyToEmail
     */
    public function setReplyToEmail(string $replyToEmail)
    {
        $this->replyToEmail = $replyToEmail;
    }

    /**
     * @return string
     */
    public function getCustomReceivers(): string
    {
        return $this->customReceivers;
    }

    /**
     * @param string $customReceivers
     */
    public function setCustomReceivers(string $customReceivers)
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

    /**
     * @param ObjectStorage $periods
     */
    public function setPeriods(ObjectStorage $periods)
    {
        $this->periods = $periods;
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     */
    public function setLocked(bool $locked)
    {
        $this->locked = $locked;
    }

    /**
     * @return array
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
     * @param array $commandData
     * @internal
     */
    public function setCommandData(array $commandData)
    {
        $this->commandData = serialize($commandData);
        $this->commandDataUnserialized = [];
    }

    /**
     * Get all receivers of this E-Mail instance using this method. Works
     * with all receiver types!
     *
     * @param array|null $orders reference if you need the associated orders if type is RECEIVER_TYPE_PERIODS
     * @return array
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

    protected function getReceiversTypePeriods(array &$orders = null)
    {
        $orders = [];
        $emails = [];
        foreach ($this->getPeriods() as $period) {
            foreach ($period->getOrders() as $order) {
                if (
                    $order->getOrderType() === Order::TYPE_ARCHIVED
                    || in_array($order->getUid(), $this->getCommandData(), true)
                ) {
                    // archived or already processed
                    continue;
                }
                $emails[$order->getUid()] = $order->getEmail();
                $orders[$order->getUid()] = $order;
            }
        }
        return $emails;
    }
}
