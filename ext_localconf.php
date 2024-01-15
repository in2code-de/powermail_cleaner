<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Powermail\Controller\FormController::class] = [
        'className' => \In2code\PowermailCleaner\Controller\FormController::class
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Powermail\Domain\Model\Mail::class] = [
        'className' => \In2code\PowermailCleaner\Domain\Model\Mail::class
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Powermail\Domain\Repository\MailRepository::class] = [
        'className' => \In2code\PowermailCleaner\Domain\Repository\MailRepository::class
    ];

    $GLOBALS['TYPO3_CONF_VARS']['LOG']['In2code']['PowermailCleaner']['Command']['CleanupCommand']['writerConfiguration'] = [
        \Psr\Log\LogLevel::INFO => [
            \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath() . '/log/powermail-cleaner_7ac500bce5.log'
            ],
        ]
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class]['flexParsing'][]
        = \In2code\PowermailCleaner\Hooks\FlexFormHook::class;

    $cmsLayout = 'cms/layout/class.tx_cms_layout.php';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$cmsLayout]['tt_content_drawItem']['powermail'] =
        \In2code\PowermailCleaner\Hooks\PluginPreview::class;

    $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
    $signalSlotDispatcher->connect(
        \In2code\Powermail\Controller\FormController::class,  // Signal class name
        'createActionAfterMailDbSaved',                                  // Signal name
        \In2code\PowermailCleaner\Hooks\AfterMailSave::class,        // Slot class name
        'attachPlugin'                               // Slot name
    );

    $signalSlotDispatcher->connect(
        \In2code\Powermail\Controller\FormController::class,  // Signal class name
        'checkIfMailIsAllowedToSave',                                  // Signal name
        \In2code\PowermailCleaner\Hooks\CheckIfMailIsAllowedToSave::class,        // Slot class name
        'check'                               // Slot name
    );
});
