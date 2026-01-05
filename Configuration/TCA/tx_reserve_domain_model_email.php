<?php

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use JWeiland\Reserve\Domain\Model\Email;
use TYPO3\CMS\Core\Utility\PathUtility;

$localLangGeneral = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf';
if (!is_file(PathUtility::getAbsoluteWebPath($localLangGeneral))) {
    $localLangGeneral = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf';
}

return [
    'ctrl' => [
        'label' => 'subject',
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'title' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email',
        'delete' => 'deleted',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'languageField' => 'sys_language_uid',
        'translationSource' => 'l18n_source',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'subject',
        'typeicon_classes' => [
            'default' => 'tx_reserve_domain_model_email',
        ],
        'type' => 'receiver_type',
    ],
    'types' => [
        Email::RECEIVER_TYPE_PERIODS => [
            'showitem' => 'subject,body,receiver_type,periods,locked,command_data,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language',
        ],
        Email::RECEIVER_TYPE_MANUAL => [
            'showitem' => 'subject,body,receiver_type,--palette--;;mail_from,--palette--;;reply_to,custom_receivers,locked,command_data,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language',
        ],
    ],
    'palettes' => [
        'mail_from' => [
            'showitem' => 'from_name,from_email',
        ],
        'reply_to' => [
            'showitem' => 'reply_to_name,reply_to_email',
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
                'foreign_table' => 'tx_reserve_domain_model_email',
                'foreign_table_where' => 'AND tx_reserve_domain_model_email.pid=###CURRENT_PID### AND tx_reserve_domain_model_email.sys_language_uid IN (-1,0)',
                'default' => 0,
            ],
        ],
        'l18n_source' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'subject' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.subject',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'body' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.body',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'softref' => 'typolink_tag,email[subst],url',
                'eval' => 'trim',
                'required' => true,
                'enableRichtext' => true,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'receiver_type' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.receiver_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => Email::RECEIVER_TYPE_PERIODS,
                'items' => [
                    [
                        'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.receiver_type.0',
                        'value' => Email::RECEIVER_TYPE_PERIODS,
                    ],
                    [
                        'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.receiver_type.1',
                        'value' => Email::RECEIVER_TYPE_MANUAL,
                    ],
                ],
            ],
        ],
        'from_name' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.from_name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
            ],
        ],
        'from_email' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.from_email',
            'config' => [
                'type' => 'email',
                'size' => 50,
            ],
        ],
        'reply_to_name' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.reply_to_name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
            ],
        ],
        'reply_to_email' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.reply_to_email',
            'config' => [
                'type' => 'email',
                'size' => 50,
            ],
        ],
        'custom_receivers' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.custom_receivers',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'required' => true,
            ],
        ],
        'periods' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.periods',
            'config' => [
                'type' => 'group',
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
                'minitems' => 1,
            ],
        ],
        'locked' => [
            'label' => 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_email.locked',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'readOnly' => true,
            ],
        ],
        'command_data' => [
            'config' => ['type' => 'passthrough'],
        ],
    ],
];
