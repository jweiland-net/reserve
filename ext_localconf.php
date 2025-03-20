<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

use JWeiland\Reserve\Controller\CheckoutController;
use JWeiland\Reserve\Controller\ManagementController;
use JWeiland\Reserve\Form\Element\QrCodePreviewElement;
use JWeiland\Reserve\Hooks\DataHandler;
use JWeiland\Reserve\Hooks\PageRenderer;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

if (!Environment::isComposerMode()) {
    require_once ExtensionManagementUtility::extPath('reserve') . '/Resources/Private/Php/vendor/autoload.php';
}

call_user_func(static function () {
    ExtensionUtility::configurePlugin(
        'Reserve',
        'Reservation',
        [
            CheckoutController::class => 'list,form,create,confirm,cancel',
        ],
        [
            CheckoutController::class => 'form,create,confirm,cancel',
        ]
    );

    ExtensionUtility::configurePlugin(
        'Reserve',
        'Management',
        [
            ManagementController::class => 'overview,period,periodsOnSameDay,scanner,scan',
        ],
        [
            ManagementController::class => 'overview,period,periodsOnSameDay,scanner,scan',
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1590659241206] = [
        'nodeName' => 'reserveQrCodePreview',
        'priority' => '70',
        'class' => QrCodePreviewElement::class,
    ];

    // ToDo: Migrate to Configuration/Icons.php while removing TYPO3 10 compatibility
    $icons = ['facility', 'order', 'order_1', 'period', 'reservation', 'email'];
    $iconRegistry = GeneralUtility::makeInstance(
        IconRegistry::class
    );
    foreach ($icons as $model) {
        $identifier = 'tx_reserve_domain_model_' . $model;
        $iconRegistry->registerIcon(
            $identifier,
            SvgIconProvider::class,
            ['source' => 'EXT:reserve/Resources/Public/Icons/' . $identifier . '.svg']
        );
    }
    $iconRegistry->registerIcon(
        'ext-reserve-wizard-icon',
        SvgIconProvider::class,
        ['source' => 'EXT:reserve/Resources/Public/Icons/Extension.svg']
    );

    // Add reserve plugin to new element wizard
    ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:reserve/Configuration/TSconfig/ContentElementWizard.tsconfig">'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][]
        = DataHandler::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][]
        = DataHandler::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][]
        = PageRenderer::class . '->processTxReserveModalUserSetting';
});
