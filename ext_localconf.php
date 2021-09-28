<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function () {
    /**
     * CommandController for powermail tasks
     */
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
        \In2code\PowermailCleaner\Command\CleanupAnswersCommandController::class;
});
