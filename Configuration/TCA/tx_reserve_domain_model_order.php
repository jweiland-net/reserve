<?php

$localLangGeneral = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf';
if (!is_file(\TYPO3\CMS\Core\Utility\PathUtility::getAbsoluteWebPath($localLangGeneral))) {
    $localLangGeneral = 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf';
}

return [
    'ctrl' => [
        'label' => 'email',
        'label_alt' => 'booked_period',
        'label_alt_force' => true,
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'title' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order',
        'delete' => 'deleted',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'languageField' => 'sys_language_uid',
        'translationSource' => 'l18n_source',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'searchFields' => 'email,activation_code',
        'type' => 'order_type',
        'typeicon_column' => 'order_type',
        'typeicon_classes' => [
            \JWeiland\Reserve\Domain\Model\Order::TYPE_DEFAULT => 'tx_reserve_domain_model_order',
            \JWeiland\Reserve\Domain\Model\Order::TYPE_ARCHIVED => 'tx_reserve_domain_model_order_1',
        ]
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => $localLangGeneral . ':LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'reserveCheckboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ]
        ],
        'sys_language_uid' => [
            'exclude' => true,
            'label' => $localLangGeneral . ':LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        $localLangGeneral . ':LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ],
                ],
                'default' => 0,
            ]
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
                        0
                    ]
                ],
                'foreign_table' => 'tx_reserve_domain_model_order',
                'foreign_table_where' => 'AND tx_reserve_domain_model_order.pid=###CURRENT_PID### AND tx_reserve_domain_model_order.sys_language_uid IN (-1,0)',
                'default' => 0
            ]
        ],
        'l18n_source' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'order_type' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.order_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.order_type.default', \JWeiland\Reserve\Domain\Model\Order::TYPE_DEFAULT],
                    ['LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.order_type.archived', \JWeiland\Reserve\Domain\Model\Order::TYPE_ARCHIVED]
                ]
            ]
        ],
        'booked_period' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_period',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_reserve_domain_model_period',
                'maxitems' => 1,
                'minitems' => 1,
                'size' => 1,
                'default' => 0,
            ]
        ],
        'activated' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.activated',
            'config' => [
                'type' => 'check',
                'renderType' => 'reserveCheckboxToggle',
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
        'email' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_order.email',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'email,required',
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
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
    ],
    'types' => [
        \JWeiland\Reserve\Domain\Model\Order::TYPE_DEFAULT => [
            'showitem' => 'order_type,booked_period,email,activated,activation_code,reservations,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language'
        ],
        \JWeiland\Reserve\Domain\Model\Order::TYPE_ARCHIVED => [
            'showitem' => 'order_type,booked_period,activated,activation_code,reservations,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language'
        ]
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
    ]
];
