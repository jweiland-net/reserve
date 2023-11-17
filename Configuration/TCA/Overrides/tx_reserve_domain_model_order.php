<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(static function (): void {
    $typo3Version = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Information\Typo3Version::class
    );
    if (version_compare($typo3Version->getBranch(), '12.0', '<')) {
        $GLOBALS['TCA']['tx_reserve_domain_model_order']['ctrl']['cruser_id'] = 'cruser_id';
    }
});
