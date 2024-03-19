<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Controller;

use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class FormController extends \In2code\Powermail\Controller\FormController {
    /**
     * @return ContentObjectRenderer
     */
    public function getContentObject() {
        return $this->contentObject;
    }

    public function initializeFormAction()
    {
        if (empty($this->settings['powermailCleaner']['deletionBehavior'])) {
            $message = GeneralUtility::makeInstance(FlashMessage::class,
                LocalizationUtility::translate('LLL:EXT:powermail_cleaner/Resources/Private/Language/locallang_db.xlf:pluginInfo.noCleanerConfiguration.message'),
                LocalizationUtility::translate('LLL:EXT:powermail_cleaner/Resources/Private/Language/locallang_db.xlf:pluginInfo.noCleanerConfiguration.title'),
                FlashMessage::ERROR,
                true
            );

            $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
            $messageQueue = $flashMessageService->getMessageQueueByIdentifier('extbase.flashmessages.tx_powermail_pi1');
            $messageQueue->addMessage($message);
        }
    }

}
