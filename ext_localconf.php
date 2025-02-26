<?php

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use JWeiland\Reserve\Controller\CheckoutController;
use JWeiland\Reserve\Controller\ManagementController;
use JWeiland\Reserve\Form\Element\QrCodePreviewElement;
use JWeiland\Reserve\Hook\DataHandlerHook;
use JWeiland\Reserve\Hook\PageRendererHook;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

if (!Environment::isComposerMode()) {
    require_once ExtensionManagementUtility::extPath('reserve') . '/Resources/Private/Php/vendor/autoload.php';
}

call_user_func(static function (): void {
    ExtensionUtility::configurePlugin(
        'Reserve',
        'Reservation',
        [
            CheckoutController::class => 'list,form,create,confirm,cancel',
        ],
        [
            CheckoutController::class => 'form,create,confirm,cancel',
        ],
    );

    ExtensionUtility::configurePlugin(
        'Reserve',
        'Management',
        [
            ManagementController::class => 'overview,period,periodsOnSameDay,scanner,scan',
        ],
        [
            ManagementController::class => 'overview,period,periodsOnSameDay,scanner,scan',
        ],
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1590659241206] = [
        'nodeName' => 'reserveQrCodePreview',
        'priority' => '70',
        'class' => QrCodePreviewElement::class,
    ];

    // Add reserve plugin to new element wizard
    ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:reserve/Configuration/TSconfig/ContentElementWizard.tsconfig">',
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][]
        = DataHandlerHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][]
        = DataHandlerHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][]
        = PageRendererHook::class . '->processTxReserveModalUserSetting';
});
