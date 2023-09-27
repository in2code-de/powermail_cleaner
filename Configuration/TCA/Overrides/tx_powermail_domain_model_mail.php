<?php

defined('TYPO3') || die('Access denied.');

use In2code\Powermail\Domain\Model\Mail;

$columns = [
    'plugin' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:powermail_cleaner/Resources/Private/Language/locallang_db.xlf:' . Mail::TABLE_NAME . '.form',
        'config' => [
            'type' => 'input',
            'size' => 40,
            'readOnly' => true,
            'eval' => 'num,trim',
        ],
    ]
];

$GLOBALS['TCA'][Mail::TABLE_NAME]['columns'] = array_merge(
    $GLOBALS['TCA'][Mail::TABLE_NAME]['columns'],
    $columns
);