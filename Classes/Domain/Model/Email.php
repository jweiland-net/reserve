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

namespace JWeiland\Reserve\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Email extends AbstractEntity
{
    /**
     * @var string
     */
    protected $subject = '';

    /**
     * @var string
     */
    protected $body = '';

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
}
