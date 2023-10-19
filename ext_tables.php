<?php

defined('TYPO3') || die('Access denied.');

call_user_func(
    function () {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Powermail\Controller\FormController::class] = [
            'className' => \In2code\PowermailCleaner\Controller\FormController::class
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Powermail\Domain\Model\Mail::class] = [
            'className' => \In2code\PowermailCleaner\Domain\Model\Mail::class
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Powermail\Domain\Repository\MailRepository::class] = [
            'className' => \In2code\PowermailCleaner\Domain\Repository\MailRepository::class
        ];
    }
);
