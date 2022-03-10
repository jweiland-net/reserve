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
        'searchFields' => 'name,short_name',
        'typeicon_classes' => [
            'default' => 'tx_reserve_domain_model_facility'
        ]
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
        'short_name' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.short_name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 8,
                'eval' => 'trim'
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
        'from_name' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.from_name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
            ],
        ],
        'from_email' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.from_email',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'email',
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
                'cols' => 40,
                'rows' => 15,
                'enableRichtext' => true,
                'default' => <<<DEFAULT_CONFIRMATION
<p>Dear visitor,</p>
<p>thank you for your reservation.</p>

<p>###ORDER_DETAILS###</p>
DEFAULT_CONFIRMATION
                ,
                'eval' => 'trim,required',
                'softref' => 'typolink_tag,images,email[subst],url',
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
                'cols' => 40,
                'rows' => 15,
                'enableRichtext' => true,
                'default' => <<<DEFAULT_RESERVATION
<p>Dear visitor,</p>
<p>thank you for your reservation.</p>

<p>###RESERVATION###</p>
DEFAULT_RESERVATION
                ,
                'eval' => 'trim,required',
                'softref' => 'typolink_tag,images,email[subst],url',
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ]
        ],
        'qr_code_example' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.qr_code_preview',
            'config' => [
                'type' => 'none',
                'renderType' => 'reserveQrCodePreview'
            ]
        ],
        'qr_code_size' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.qr_code_size',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,int',
                'range' => [
                    'lower' => 10,
                    'upper' => 800,
                ],
                'default' => 350,
                'slider' => [
                    'step' => 10,
                    'width' => 200,
                ],
            ]
        ],
        'qr_code_label_size' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.qr_code_label_size',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,int',
                'range' => [
                    'lower' => 10,
                    'upper' => 800,
                ],
                'default' => 16,
                'slider' => [
                    'step' => 10,
                    'width' => 200,
                ],
            ]
        ],
        'qr_code_logo' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.qr_code_logo',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'qr_code_logo',
                [
                    'minitems' => 0,
                    'maxitems' => 1,
                    'foreign_match_fields' => [
                        'fieldname' => 'qr_code_logo',
                        'tablenames' => 'tx_reserve_domain_model_facility',
                        'table_local' => 'sys_file',
                    ],
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                    'overrideChildTca' => [
                        'types' => [
                            '0' => [
                                'showitem' => '
                                --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                                --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                            ],
                        ],
                    ],
                ],
                'png,jpg,jpeg,gif,bmp'
            )
        ],
        'qr_code_logo_width' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.qr_code_logo_width',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,int',
                'range' => [
                    'lower' => 10,
                    'upper' => 800,
                ],
                'default' => 150,
                'slider' => [
                    'step' => 10,
                    'width' => 200,
                ],
            ]
        ],
        'cancelable' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.cancelable',
            'onChange' => 'reload',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                    ]
                ],
                'default' => true
            ]
        ],
        'cancelable_until_minutes' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.cancelable_until_minutes',
            'displayCond' => 'FIELD:cancelable:REQ:true',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'trim,num',
                'default' => 60,
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => 'name,short_name,cancelable,cancelable_until_minutes,periods,--div--;LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.div.mail_settings,
            --palette--;;mail_from,--palette--;;reply_to,confirmation_mail_subject,confirmation_mail_html,reservation_mail_subject,reservation_mail_html,
            --div--;LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.div.qr_code,qr_code_example,qr_code_size,qr_code_label_size,qr_code_logo,qr_code_logo_width,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language'
        ],
    ],
    'palettes' => [
        'mail_from' => [
            'showitem' => 'from_name,from_email'
        ],
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
