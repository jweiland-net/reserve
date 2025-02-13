<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

if (!defined('TYPO3')) {
    die('Access denied.');
}

ExtensionManagementUtility::addStaticFile(
    'reserve',
    'Configuration/TypoScript',
    'Reserve'
);

ExtensionManagementUtility::addStaticFile(
    'reserve',
    'Configuration/TypoScript/Scanner',
    'Reserve scanner'
);

ExtensionManagementUtility::addStaticFile(
    'reserve',
    'Configuration/TypoScript/Reservation',
    'Reserve reservation'
);
