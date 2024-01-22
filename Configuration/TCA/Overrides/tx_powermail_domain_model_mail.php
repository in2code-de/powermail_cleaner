<?php
defined('TYPO3_MODE') || die('Access denied.');

use In2code\Powermail\Domain\Model\Mail;

$columns = [
    'deletion_timestamp' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:powermail_cleaner/Resources/Private/Language/locallang_db.xlf:mail.deletionDate',
        'config' => [
            'type' => 'input',
            'renderType' => 'inputDateTime',
            'required' => true,
            'size' => 20,
            'max' => 1024,
            'eval' => 'date,int',
            'default' => 0,
        ],
    ],
    'plugin' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:powermail_cleaner/Resources/Private/Language/locallang_db.xlf:mail.related-plugin',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'foreign_table' => 'tt_content',
            'foreign_table_where' => 'and tt_content.deleted = 0 order by tt_content.header',
            'readOnly' => true,
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(Mail::TABLE_NAME, $columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(Mail::TABLE_NAME, 'deletion_timestamp,plugin', '', 'after:crdate');
