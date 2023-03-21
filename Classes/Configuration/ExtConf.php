<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Configuration;

use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Extension configuration for EXT:reserve
 */
class ExtConf implements SingletonInterface
{
    private $blockMultipleOrdersInSeconds = 3600;

    public function __construct(ExtensionConfiguration $extensionConfiguration)
    {
        try {
            $extConf = $extensionConfiguration->get('reserve');
            if (is_array($extConf)) {
                // call setter method foreach configuration entry
                foreach ($extConf as $key => $value) {
                    $methodName = 'set' . ucfirst($key);
                    if (method_exists($this, $methodName)) {
                        $this->$methodName($value);
                    }
                }
            }
        } catch (ExtensionConfigurationExtensionNotConfiguredException | ExtensionConfigurationPathDoesNotExistException $e) {
        }
    }

    public function getBlockMultipleOrdersInSeconds(): int
    {
        return $this->blockMultipleOrdersInSeconds;
    }

    public function setBlockMultipleOrdersInSeconds(string $blockMultipleOrdersInSeconds): void
    {
        $this->blockMultipleOrdersInSeconds = (int)$blockMultipleOrdersInSeconds;
    }
}
