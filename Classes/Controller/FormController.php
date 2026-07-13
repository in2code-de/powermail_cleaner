<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Controller;

use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\ArrayUtility;
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
       if (abs($this->contentObject->data['sys_language_uid']) > 0)
       {
           ArrayUtility::mergeRecursiveWithOverrule($this->settings, $this->getParentLocalePowermailCleanerSettings());
       }

       if ((empty($this->settings['powermailCleaner']) || $this->settings['powermailCleaner']['deletionBehavior']==='') && ((int)$this->settings['powermail_cleaner_enabled'] === 1)) {
            $message = GeneralUtility::makeInstance(FlashMessage::class,
                LocalizationUtility::translate('LLL:EXT:powermail_cleaner/Resources/Private/Language/locallang_db.xlf:pluginInfo.noCleanerConfiguration.message'),
                LocalizationUtility::translate('LLL:EXT:powermail_cleaner/Resources/Private/Language/locallang_db.xlf:pluginInfo.noCleanerConfiguration.title'),
                ContextualFeedbackSeverity::ERROR,
                true
            );
            $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
            $messageQueue = $flashMessageService->getMessageQueueByIdentifier('extbase.flashmessages.tx_powermail_pi1');
            $messageQueue->addMessage($message);
        }
    }

    protected function getParentLocalePowermailCleanerSettings(): array
    {
        $contentObjectData = $this->contentObject->data ?? [];
        $parentUid = (int)($contentObjectData['l18n_parent'] ?? 0);

        if ($parentUid <= 0) {
            return [];
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tt_content');

        $row = $queryBuilder
            ->select('pi_flexform')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($parentUid, Connection::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'deleted',
                    $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAssociative();

        if (empty($row['pi_flexform'])) {
            return [];
        }

        $flexFormArray = GeneralUtility::makeInstance(FlexFormService::class)
            ->convertFlexFormContentToArray($row['pi_flexform']);

        $settings = $flexFormArray['settings']['flexform']['powermailCleaner'] ?? [];

        return is_array($settings) ? $settings : [];
    }
}
