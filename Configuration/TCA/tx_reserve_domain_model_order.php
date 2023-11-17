<?php

$localLangGeneral = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf';
if (!is_file(\TYPO3\CMS\Core\Utility\PathUtility::getAbsoluteWebPath($localLangGeneral))) {
    $localLangGeneral = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf';
}

return [
    'ctrl' => [
        'label' => 'last_name',
        'label_alt' => 'first_name,email,booked_period',
        'label_alt_force' => true,
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'title' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order',
        'delete' => 'deleted',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'languageField' => 'sys_language_uid',
        'translationSource' => 'l18n_source',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'email,activation_code',
        'typeicon_classes' => [
            'default' => 'tx_reserve_domain_model_order',
        ],
    ],
    'types' => [
        0 => [
            'showitem' => 'booked_period,--palette--;;name,--palette--;;contact,--palette--;;address,organization,remarks,activated,activation_code,reservations,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language',
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
        'name' => [
            'showitem' => 'last_name,first_name',
        ],
        'contact' => [
            'showitem' => 'email,phone',
        ],
        'address' => [
            'showitem' => 'address,--linebreak--,zip,city',
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
                        0 => '',
                        1 => '',
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
                        '',
                        0,
                    ],
                ],
                'foreign_table' => 'tx_reserve_domain_model_order',
                'foreign_table_where' => 'AND tx_reserve_domain_model_order.pid=###CURRENT_PID### AND tx_reserve_domain_model_order.sys_language_uid IN (-1,0)',
                'default' => 0,
            ],
        ],
        'l18n_source' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'booked_period' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.booked_period',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_reserve_domain_model_period',
                'maxitems' => 1,
                'minitems' => 1,
                'size' => 1,
                'default' => 0,
            ],
        ],
        'activated' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.activated',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'activation_code' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.activation_code',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
            ],
        ],
        'first_name' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.first_name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'required',
            ],
        ],
        'last_name' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.last_name',
            'config' => [
                'type' => 'input',
                'size' => 50,
            ],
        ],
        'email' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.email',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'email,required',
            ],
        ],
        'phone' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.phone',
            'config' => [
                'type' => 'input',
                'size' => 50,
            ],
        ],
        'address' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.address',
            'config' => [
                'type' => 'input',
                'size' => 50,
            ],
        ],
        'zip' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.zip',
            'config' => [
                'type' => 'input',
                'size' => 50,
            ],
        ],
        'city' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.city',
            'config' => [
                'type' => 'input',
                'size' => 50,
            ],
        ],
        'organization' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.organization',
            'config' => [
                'type' => 'input',
                'size' => 100,
                'default' => '',
                'eval' => 'trim',
            ],
        ],
        'remarks' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.remarks',
            'config' => [
                'type' => 'text',
                'cols' => 50,
                'rows' => 3,
                'default' => '',
                'eval' => 'trim',
            ],
        ],
        'reservations' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.reservations',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_reserve_domain_model_reservation',
                'foreign_field' => 'customer_order',
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
