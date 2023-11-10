<?php
defined('TYPO3_MODE') || die('Access denied.');

use In2code\Powermail\Domain\Model\Mail;

$columns = [
    'plugin' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:powermail_cleaner/Resources/Private/Language/locallang_db.xlf:mail.related-plugin',
        'config' => [
            'type' => 'inline',
            'foreign_table' => 'tt_content',
            'readOnly' => true,
        ],
    ]
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(Mail::TABLE_NAME, $columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(Mail::TABLE_NAME, 'plugin', '', 'after:form');