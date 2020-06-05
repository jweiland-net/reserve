<?php

$localLangGeneral = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf';
if (!is_file(\TYPO3\CMS\Core\Utility\PathUtility::getAbsoluteWebPath($localLangGeneral))) {
    $localLangGeneral = 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf';
}

return [
    'ctrl' => [
        'label' => 'subject',
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'title' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email',
        'delete' => 'deleted',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'languageField' => 'sys_language_uid',
        'translationSource' => 'l18n_source',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'searchFields' => 'subject',
//        'typeicon_classes' => [
//            'default' => 'tx_reserve_domain_model_facility'
//        ]
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
        'subject' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.subject',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim,required'
            ],
        ],
        'body' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.body',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'default' => <<<DEFAULT_CONFIRMATION
<p>Dear visitor,</p>
<p>we changed some details of your booked time period!</p>

<p>Updated reservation data:</p>
<p>###RESERVATION###</p>
DEFAULT_CONFIRMATION
                ,
                'eval' => 'trim,required',
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ]
        ],
        'periods' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.periods',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_reserve_domain_model_period',
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => true,
                    ],
                    'addRecord' => [
                        'disabled' => true,
                    ],
                    'listModule' => [
                        'disabled' => false,
                    ],
                ],
                'minitems' => 1
            ],
        ],
        'locked' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.locked',
            'config' => [
                'type' => 'check',
                'renderType' => (function() {
                    return class_exists(\TYPO3\CMS\Backend\Form\Element\CheckboxToggleElement::class) ? 'checkboxToggle' : null;
                })(),
                'readOnly' => true
            ],
        ],
        'command_data' => [
            'config' => ['type' => 'passthrough']
        ]
    ],
    'types' => [
        '1' => [
            'showitem' => 'subject,body,periods,locked,command_data,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language'
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
    ]
];
