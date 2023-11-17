<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'reserve',
    'Configuration/TypoScript',
    'Reserve'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'reserve',
    'Configuration/TypoScript/Scanner',
    'Reserve scanner'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'reserve',
    'Configuration/TypoScript/Reservation',
    'Reserve reservation'
);
