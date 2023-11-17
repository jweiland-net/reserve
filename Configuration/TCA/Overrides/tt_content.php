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

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Reserve',
    'Reservation',
    'LLL:EXT:reserve/Resources/Private/Language/locallang.xlf:plugin.reservation.title'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Reserve',
    'Management',
    'LLL:EXT:reserve/Resources/Private/Language/locallang.xlf:plugin.management.title'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['reserve_reservation'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'reserve_reservation',
    'FILE:EXT:reserve/Configuration/FlexForms/Reservation.xml'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['reserve_reservation'] = 'recursive,pages';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['reserve_management'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'reserve_management',
    'FILE:EXT:reserve/Configuration/FlexForms/Management.xml'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['reserve_management'] = 'recursive,pages';
