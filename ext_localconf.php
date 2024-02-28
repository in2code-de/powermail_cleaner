<?php

defined('TYPO3') || die('Access denied.');

call_user_func(function () {
    $GLOBALS['TYPO3_CONF_VARS']['LOG']['In2code']['PowermailCleaner']['Command']['CleanupCommand']['writerConfiguration'] = [
        \Psr\Log\LogLevel::INFO => [
            \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath() . '/log/powermail-cleaner_7ac500bce5.log'
            ],
        ]
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Powermail\Controller\FormController::class] = [
        'className' => \In2code\PowermailCleaner\Controller\FormController::class
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Powermail\Domain\Model\Mail::class] = [
        'className' => \In2code\PowermailCleaner\Domain\Model\Mail::class
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Powermail\Domain\Repository\MailRepository::class] = [
        'className' => \In2code\PowermailCleaner\Domain\Repository\MailRepository::class
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesService::class] = [
        'className' => \In2code\PowermailCleaner\Domain\Service\ReceiverAddressService::class
    ];

    $GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths'][1699609953] =
        'EXT:powermail_cleaner/Resources/Private/Templates/Email';

    $GLOBALS['TYPO3_CONF_VARS']['MAIL']['layoutRootPaths'][1699609953] =
        'EXT:powermail_cleaner/Resources/Private/Layouts/Email';
});
