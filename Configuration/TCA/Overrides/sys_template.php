<?php

defined('TYPO3') || die('Access denied.');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'powermail_cleaner',
    'Configuration/TypoScript',
    'Powermail Cleaner Template'
);