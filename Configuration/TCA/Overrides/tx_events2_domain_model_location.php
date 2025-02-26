<?php

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(static function () {
    if (ExtensionManagementUtility::isLoaded('events2')) {
        $ll = 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:';

        $locationColumns = [
            'facility' => [
                'exclude' => 1,
                'label' => $ll . 'tx_events2_domain_model_location.facility',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'foreign_table' => 'tx_reserve_domain_model_facility',
                    'foreign_table_where' => 'AND 1=1 ORDER BY tx_reserve_domain_model_facility.name ASC',
                    'items' => [
                        ['', '0'],
                    ],
                    'minitems' => 0,
                    'maxitems' => 1,
                    'default' => 0,
                ],
            ],
        ];

        ExtensionManagementUtility::addTCAcolumns(
            'tx_events2_domain_model_location',
            $locationColumns,
        );

        ExtensionManagementUtility::addToAllTCAtypes(
            'tx_events2_domain_model_location',
            'facility',
            '',
            'after:location',
        );
    }
});
