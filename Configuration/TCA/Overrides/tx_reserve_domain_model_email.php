<?php

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(static function (): void {
    $typo3Version = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Information\Typo3Version::class
    );
    if (version_compare($typo3Version->getBranch(), '12.0', '<')) {
        $GLOBALS['TCA']['tx_reserve_domain_model_email']['ctrl']['cruser_id'] = 'cruser_id';
    }
});
