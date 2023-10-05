<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\EventListener;

use In2code\Powermail\Events\BackendPageModulePreviewContentEvent;
use TYPO3\CMS\Core\Service\FlexFormService;
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

        $flexforms = $flexFormService->convertFlexFormContentToArray($record['pi_flexform']);

        if ($flexforms['settings']['flexform']['powermailCleaner']['deletionBehavior'] !== '') {
            $event->setPreview(
        $existingPreview . $this->getCleanerPreview($flexforms['settings']['flexform']['powermailCleaner'])
            );
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
                'deletionDate' => $cleanerConfiguration['deletionDate'],
                'deletionPeriod' => $cleanerConfiguration['deletionPeriod'],
            ]
        );
        return $standaloneView->render();
    }
}
