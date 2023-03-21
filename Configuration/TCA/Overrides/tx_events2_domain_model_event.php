<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(static function () {
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('events2')) {
        $ll = 'LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:';

        $eventColumns = [
            'registration_required' => [
                'exclude' => 1,
                'label' => $ll . 'tx_events2_domain_model_event.registration_required',
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxToggle',
                    'default' => 0,
                ],
            ],
        ];

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
            'tx_events2_domain_model_event',
            $eventColumns
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            'tx_events2_domain_model_event',
            'registration_required',
            'single,recurring',
            'before:teaser'
        );
    }
});
