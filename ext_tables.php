<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
call_user_func(
    function () {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['Powermail']['modules']['web_PowermailM1']['controllers']['Module']['actions'][] = 'cleanup';

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Powermail\Controller\ModuleController::class] = [
            'className' => \In2code\PowermailCleaner\Controller\ModuleController::class
        ];
    }
);
