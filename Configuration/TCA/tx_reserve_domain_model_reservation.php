<?php

use TYPO3\CMS\Core\Utility\PathUtility;

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

$localLangGeneral = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf';
if (!is_file(PathUtility::getAbsoluteWebPath($localLangGeneral))) {
    $localLangGeneral = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf';
}

return [
    'ctrl' => [
        'label' => 'code',
        'label_alt' => 'last_name,first_name',
        'label_alt_force' => true,
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'title' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_reservation',
        'delete' => 'deleted',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'languageField' => 'sys_language_uid',
        'translationSource' => 'l18n_source',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'code',
        'typeicon_classes' => [
            'default' => 'tx_reserve_domain_model_reservation',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => 'customer_order,first_name,last_name,code,used,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language',
        ],
    ],
    'palettes' => [
        'hidden' => [
            'showitem' => '
                hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:field.default.hidden
            ',
        ],
        'language' => [
            'showitem' => '
                sys_language_uid;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:sys_language_uid_formlabel,l18n_parent
            ',
        ],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => $localLangGeneral . ':LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => '',
                        'value' => '',
                        'invertStateDisplay' => true,
                    ],
                ],
            ],
        ],
        'sys_language_uid' => [
            'exclude' => true,
            'label' => $localLangGeneral . ':LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => $localLangGeneral . ':LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => '',
                        'value' => 0,
                    ],
                ],
                'foreign_table' => 'tx_reserve_domain_model_reservation',
                'foreign_table_where' => 'AND tx_reserve_domain_model_reservation.pid=###CURRENT_PID### AND tx_reserve_domain_model_reservation.sys_language_uid IN (-1,0)',
                'default' => 0,
            ],
        ],
        'l18n_source' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'customer_order' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_reservation.customer_order',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_reserve_domain_model_order',
                'maxitems' => 1,
                'minitems' => 1,
                'size' => 1,
                'default' => 0,
            ],
        ],
        'first_name' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_reservation.first_name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'last_name' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_reservation.last_name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
            ],
        ],
        'code' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_reservation.code',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'used' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_reservation.used',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
    ],
];
