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
            'type' => 'select',
            'renderType' => 'selectSingle',
            'foreign_table' => 'tt_content',
            // only fetch selected record since field is readOnly and other options will not be selectable anyway
            'foreign_table_where' => 'and tt_content.deleted = 0 and tt_content.uid=###REC_FIELD_plugin###',
            'readOnly' => true,
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(Mail::TABLE_NAME, $columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(Mail::TABLE_NAME, 'deletion_timestamp,plugin', '', 'after:crdate');
