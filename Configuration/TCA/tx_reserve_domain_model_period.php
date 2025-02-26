<?php

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\PathUtility;

$localLangGeneral = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf';
if (!is_file(PathUtility::getAbsoluteWebPath($localLangGeneral))) {
    $localLangGeneral = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf';
}

return [
    'ctrl' => [
        'label' => 'date',
        'label_alt' => 'begin,end',
        'label_alt_force' => true,
        'default_sortby' => 'date DESC, end DESC',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'title' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_period',
        'delete' => 'deleted',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'languageField' => 'sys_language_uid',
        'translationSource' => 'l18n_source',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'name',
        'typeicon_classes' => [
            'default' => 'tx_reserve_domain_model_period',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => 'facility,--palette--;;date,--palette--;;max_participants,--palette--;;booking_restrictions,orders,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language',
        ],
    ],
    'palettes' => [
        'date' => [
            'showitem' => 'date,begin,end',
        ],
        'max_participants' => [
            'showitem' => 'max_participants,max_participants_per_order',
        ],
        'booking_restrictions' => [
            'showitem' => 'booking_begin,booking_end',
        ],
        'hidden' => [
            'showitem' => 'hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:field.default.hidden',
        ],
        'language' => [
            'showitem' => 'sys_language_uid;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:sys_language_uid_formlabel,l18n_parent',
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
                'foreign_table' => 'tx_reserve_domain_model_period',
                'foreign_table_where' => 'AND tx_reserve_domain_model_period.pid=###CURRENT_PID### AND tx_reserve_domain_model_period.sys_language_uid IN (-1,0)',
                'default' => 0,
            ],
        ],
        'l18n_source' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'facility' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_reserve_domain_model_facility',
                'maxitems' => 1,
                'minitems' => 1,
                'size' => 1,
                'default' => 0,
            ],
        ],
        'booking_begin' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_period.booking_begin',
            'config' => [
                'type' => 'datetime',
                'size' => 30,
                'eval' => 'datetime,int',
                'required' => true,
            ],
        ],
        'booking_end' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_period.booking_end',
            'config' => [
                'type' => 'datetime',
                'size' => 30,
                'eval' => 'datetime,int',
            ],
        ],
        'date' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_period.date',
            'config' => [
                'type' => 'datetime',
                'size' => 30,
                'eval' => 'date,int',
                'required' => true,
            ],
        ],
        'begin' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_period.begin',
            'config' => [
                'type' => 'datetime',
                'size' => 30,
                'eval' => 'time,int',
                'required' => true,
            ],
        ],
        'end' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_period.end',
            'config' => [
                'type' => 'datetime',
                'size' => 30,
                'eval' => 'time,int',
            ],
        ],
        'max_participants' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_period.max_participants',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'num',
                'required' => true,
                'range' => [
                    'lower' => 1,
                ],
            ],
        ],
        'max_participants_per_order' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_period.max_participants_per_order',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'num',
                'required' => true,
                'range' => [
                    'lower' => 1,
                ],
            ],
        ],
        'orders' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_period.orders',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_reserve_domain_model_order',
                'foreign_field' => 'booked_period',
                'maxitems' => 500,
                'appearance' => [
                    'collapseAll' => true,
                    'expandSingle' => true,
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
    ],
];
