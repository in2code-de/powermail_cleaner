<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\EventListener;

use In2code\Powermail\Events\BackendPageModulePreviewContentEvent;
use In2code\PowermailCleaner\Utility\BackendUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

final class BackendPageModulePreviewContentEventListener
{
    private string $templatePathAndFileName = 'EXT:powermail_cleaner/Resources/Private/Templates/PluginPreview.html';

    public function __invoke(BackendPageModulePreviewContentEvent $event): void
    {
        $existingPreview = $event->getPreview();
        $record = $event->getItem()->getRecord();
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);

        if ($record['l18n_parent'] === 0) {
            $flexforms = $flexFormService->convertFlexFormContentToArray($record['pi_flexform']);
        } else {
            $flexforms = $flexFormService->convertFlexFormContentToArray(
                BackendUtility::getL18nParentFlexForm($record['l18n_parent'])
            );
        }

        if (ArrayUtility::isValidPath($flexforms, 'settings/flexform/powermailCleaner')){
            $cleanerSettings = $flexforms['settings']['flexform']['powermailCleaner'];

            if ($cleanerSettings['deletionBehavior'] !== '') {
                $event->setPreview(
                    $existingPreview . $this->getCleanerPreview($cleanerSettings)
                );
            }
        }
    }

    protected function getCleanerPreview(array $cleanerConfiguration)
    {
        /** @var StandaloneView $standaloneView */
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $standaloneView->setFormat('html');
        $standaloneView->setTemplatePathAndFilename($this->templatePathAndFileName);
        $standaloneView->assignMultiple(
            [
                'deletionBehavior' => $cleanerConfiguration['deletionBehavior'],
                'deletionDate' => $cleanerConfiguration['deletionDate'] ?? '',
                'deletionPeriod' => $cleanerConfiguration['deletionPeriod'] ?? '',
                'informReceiversBeforeDeletion' => $cleanerConfiguration['informReceiversBeforeDeletion'] ?? '',
                'informReceiversBeforeDeletionPeriod' => $cleanerConfiguration['informReceiversBeforeDeletionPeriod'] ?? '',
            ]
        );
        return $standaloneView->render();
    }
}
