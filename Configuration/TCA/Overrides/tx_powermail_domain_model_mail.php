<?php

defined('TYPO3') || die('Access denied.');

use In2code\Powermail\Domain\Model\Mail;

$columns = [
    'deletion_timestamp' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:powermail_cleaner/Resources/Private/Language/locallang_db.xlf:mail.deletionDate',
        'config' => [
            'type' => 'datetime',
            'format' => 'datetime',
            'eval' => 'int',
            'readOnly' => true,
        ],
    ],
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
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(Mail::TABLE_NAME, 'deletion_timestamp,plugin', '', 'after:crdate');