<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

if (!\TYPO3\CMS\Core\Core\Environment::isComposerMode()) {
    require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('reserve') . '/Resources/Private/Php/vendor/autoload.php';
}

call_user_func(static function (): void {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Reserve',
        'Reservation',
        [
            \JWeiland\Reserve\Controller\CheckoutController::class => 'list,form,create,confirm,cancel',
        ],
        [
            \JWeiland\Reserve\Controller\CheckoutController::class => 'form,create,confirm,cancel',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Reserve',
        'Management',
        [
            \JWeiland\Reserve\Controller\ManagementController::class => 'overview,period,periodsOnSameDay,scanner,scan',
        ],
        [
            \JWeiland\Reserve\Controller\ManagementController::class => 'overview,period,periodsOnSameDay,scanner,scan',
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1590659241206] = [
        'nodeName' => 'reserveQrCodePreview',
        'priority' => '70',
        'class' => \JWeiland\Reserve\Form\Element\QrCodePreviewElement::class,
    ];

    // Add reserve plugin to new element wizard
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:reserve/Configuration/TSconfig/ContentElementWizard.tsconfig">'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][]
        = \JWeiland\Reserve\Hook\DataHandlerHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][]
        = \JWeiland\Reserve\Hook\DataHandlerHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][]
        = \JWeiland\Reserve\Hook\PageRendererHook::class . '->processTxReserveModalUserSetting';
});
