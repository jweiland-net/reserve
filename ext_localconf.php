<?php

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'JWeiland.Reserve',
    'Reservation',
    [
        'Checkout' => 'list,form,create,confirm'
    ],
    [
        'Checkout' => 'list,form,create,confirm'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'JWeiland.Reserve',
    'Management',
    [
        'Management' => 'overview,period,scanner,scan'
    ],
    [
        'Management' => 'scan'
    ]
);

if (!class_exists(\TYPO3\CMS\Extbase\Annotation\Validate::class)) {
    // TYPO3 v8 compatibility
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
plugin.tx_reserve {
    persistence {
        classes {
            JWeiland\Reserve\Domain\Model\Order {
                subclasses {
                    0 = JWeiland\Reserve\Domain\Model\v8\Order
                }
            }
            JWeiland\Reserve\Domain\Model\v8\Order {
                mapping {
                    recordType = 0
                    tableName = tx_reserve_domain_model_order
                }
            }
        }
    }
}');

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\JWeiland\Reserve\Controller\CheckoutController::class] = [
        'className' => \JWeiland\Reserve\Controller\v8\CheckoutController::class
    ];
}
