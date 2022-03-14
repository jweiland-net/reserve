<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (!\TYPO3\CMS\Core\Core\Environment::isComposerMode()) {
    require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('reserve') . '/Resources/Private/Php/vendor/autoload.php';
}

call_user_func(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Reserve',
        'Reservation',
        [
            \JWeiland\Reserve\Controller\CheckoutController::class => 'list,form,create,confirm,cancel'
        ],
        [
            \JWeiland\Reserve\Controller\CheckoutController::class => 'form,create,confirm,cancel'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Reserve',
        'Management',
        [
            \JWeiland\Reserve\Controller\ManagementController::class => 'overview,period,periodsOnSameDay,scanner,scan'
        ],
        [
            \JWeiland\Reserve\Controller\ManagementController::class => 'overview,period,periodsOnSameDay,scanner,scan'
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1590659241206] = [
        'nodeName' => 'reserveQrCodePreview',
        'priority' => '70',
        'class' => \JWeiland\Reserve\Form\Element\QrCodePreviewElement::class,
    ];

    $icons = ['facility', 'order', 'order_1', 'period', 'reservation', 'email'];
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    foreach ($icons AS $model) {
        $identifier = 'tx_reserve_domain_model_' . $model;
        $iconRegistry->registerIcon(
            $identifier,
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:reserve/Resources/Public/Icons/' . $identifier . '.svg']
        );
    }
    unset($icons, $iconRegistry);

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \JWeiland\Reserve\Hooks\DataHandler::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = \JWeiland\Reserve\Hooks\DataHandler::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] = \JWeiland\Reserve\Hooks\PageRenderer::class . '->processTxReserveModalUserSetting';
});
