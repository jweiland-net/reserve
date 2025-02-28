<?php

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use JWeiland\Reserve\Backend\Preview\ReservePluginPreview;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

ExtensionUtility::registerPlugin(
    'Reserve',
    'Reservation',
    'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:plugin.reserve_reservation.title',
    'tx_reserve_domain_model_reservation',
    'plugins',
    'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:plugin.reserve_reservation.description',
);

ExtensionUtility::registerPlugin(
    'Reserve',
    'Management',
    'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:plugin.reserve_management.title',
    'ext-reserve-wizard-icon',
    'plugins',
    'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:plugin.reserve_management.description',
);

ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:reserve/Configuration/FlexForms/Reservation.xml',
    'reserve_reservation'
);

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;Configuration,pi_flexform',
    'reserve_reservation',
    'after:subheader',
);

ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:reserve/Configuration/FlexForms/Management.xml',
    'reserve_management',
);

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;Configuration,pi_flexform',
    'reserve_management',
    'after:subheader',
);

$GLOBALS['TCA']['tt_content']['types']['reserve_reservation']['previewRenderer'] = ReservePluginPreview::class;
$GLOBALS['TCA']['tt_content']['types']['reserve_management']['previewRenderer'] = ReservePluginPreview::class;
