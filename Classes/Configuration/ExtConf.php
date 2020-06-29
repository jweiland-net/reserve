<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Configuration;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Extension configuration for EXT:reserve
 */
class ExtConf implements SingletonInterface
{
    private $blockMultipleOrdersInSeconds = 3600;

    public function __construct()
    {
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['reserve'])) {
            // get global configuration
            $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['reserve']);
            if (is_array($extConf) && count($extConf)) {
                // call setter method foreach configuration entry
                foreach ($extConf as $key => $value) {
                    $methodName = 'set' . ucfirst($key);
                    if (method_exists($this, $methodName)) {
                        $this->$methodName($value);
                    }
                }
            }
        }
    }

    /**
     * @return int
     */
    public function getBlockMultipleOrdersInSeconds(): int
    {
        return $this->blockMultipleOrdersInSeconds;
    }

    /**
     * @param mixed $blockMultipleOrdersInSeconds
     */
    public function setBlockMultipleOrdersInSeconds($blockMultipleOrdersInSeconds)
    {
        $this->blockMultipleOrdersInSeconds = (int)$blockMultipleOrdersInSeconds;
    }
}
