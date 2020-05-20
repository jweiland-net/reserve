<?php

$localLangGeneral = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf';
if (!is_file(\TYPO3\CMS\Core\Utility\PathUtility::getAbsoluteWebPath($localLangGeneral))) {
    $localLangGeneral = 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf';
}

return [
    'ctrl' => [
        'label' => 'name',
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'title' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility',
        'delete' => 'deleted',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'languageField' => 'sys_language_uid',
        'translationSource' => 'l18n_source',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'searchFields' => 'name'
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => $localLangGeneral . ':LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => (function() {
                    return class_exists(\TYPO3\CMS\Backend\Form\Element\CheckboxToggleElement::class) ? 'checkboxToggle' : null;
                })(),
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
                'foreign_table' => 'tx_reserve_domain_model_facility',
                'foreign_table_where' => 'AND tx_reserve_domain_model_facility.pid=###CURRENT_PID### AND tx_reserve_domain_model_facility.sys_language_uid IN (-1,0)',
                'default' => 0
            ]
        ],
        'l18n_source' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'name' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim,required'
            ],
        ],
        'periods' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.periods',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_reserve_domain_model_period',
                'foreign_field' => 'facility',
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
        'reply_to_name' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.reply_to_name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
            ],
        ],
        'reply_to_email' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.reply_to_email',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'email',
            ],
        ],
        'confirmation_mail_subject' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.confirmation_mail_subject',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required',
                'default' => 'Please confirm your reservation'
            ]
        ],
        'confirmation_mail_html' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.confirmation_mail_html',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'default' => <<<DEFAULT_CONFIRMATION
<p>Dear visitor,</p>
<p>thank you for your reservation.</p>

<p>###ORDER_DETAILS###</p>
DEFAULT_CONFIRMATION
                ,
                'eval' => 'trim,required',
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ]
        ],
        'reservation_mail_subject' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.reservation_mail_subject',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required',
                'default' => 'Details of your reservation'
            ]
        ],
        'reservation_mail_html' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.reservation_mail_html',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'default' => <<<DEFAULT_RESERVATION
<p>Dear visitor,</p>
<p>thank you for your reservation.</p>

<p>###RESERVATION###</p>
DEFAULT_RESERVATION
                ,
                'eval' => 'trim,required',
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ]
        ]
    ],
    'types' => [
        '1' => [
            'showitem' => 'name,periods,--div--;LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.div.mail_settings,
            --palette--;;reply_to,confirmation_mail_subject,confirmation_mail_html,reservation_mail_subject,reservation_mail_html,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language'
        ],
    ],
    'palettes' => [
        'reply_to' => [
            'showitem' => 'reply_to_name,reply_to_email'
        ],
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
