<?php

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

if (!defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'reserve',
    'Configuration/TypoScript',
    'Reserve'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'reserve',
    'Configuration/TypoScript/Scanner',
    'Reserve scanner'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'reserve',
    'Configuration/TypoScript/Reservation',
    'Reserve reservation'
);
