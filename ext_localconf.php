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

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postUserLookUp'][] =
        \In2code\PowermailCleaner\Hooks\OverwriteFlexForm::class . '->overwrite';
});
