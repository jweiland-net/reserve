<?php

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'JWeiland.Reserve',
    'Reservation',
    [
        'Checkout' => 'list,form,create,confirm,cancel'
    ],
    [
        'Checkout' => 'form,create,confirm,cancel'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'JWeiland.Reserve',
    'Management',
    [
        'Management' => 'overview,period,periodsOnSameDay,scanner,scan'
    ],
    [
        'Management' => 'overview,period,periodsOnSameDay,scanner,scan'
    ]
);

if (!class_exists(\TYPO3\CMS\Extbase\Annotation\Validate::class)) {
    // TYPO3 v8 compatibility
   \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
config.tx_extbase {
  objects {
    JWeiland\Reserve\Domain\Model\Facility {
      className = JWeiland\Reserve\Domain\Model\v8\Facility
    }
    JWeiland\Reserve\Domain\Model\Order {
      className = JWeiland\Reserve\Domain\Model\v8\Order
    }
    JWeiland\Reserve\Domain\Model\Period {
      className = JWeiland\Reserve\Domain\Model\v8\Period
    }
  }
  persistence {
    classes {
      JWeiland\Reserve\Domain\Model\v8\Facility {
        mapping {
          recordType = 0
          tableName = tx_reserve_domain_model_facility
        }
      }
      JWeiland\Reserve\Domain\Model\v8\Order {
        mapping {
          recordType = 0
          tableName = tx_reserve_domain_model_order
        }
      }
      JWeiland\Reserve\Domain\Model\v8\Period {
        mapping {
          recordType = 0
          tableName = tx_reserve_domain_model_period
        }
      }
    }
  }
}');

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\JWeiland\Reserve\Controller\CheckoutController::class] = [
        'className' => \JWeiland\Reserve\Controller\v8\CheckoutController::class
    ];
}

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

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeResolver'][1593430553393] = [
    'nodeName' => 'reserveCheckboxToggle',
    'priority' => 50,
    'class' => \JWeiland\Reserve\Form\Resolver\CheckboxToggleElementResolver::class,
];
