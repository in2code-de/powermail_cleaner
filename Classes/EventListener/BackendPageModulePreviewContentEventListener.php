<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\EventListener;

use In2code\Powermail\Events\BackendPageModulePreviewContentEvent;
use In2code\PowermailCleaner\Utility\BackendUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;

final class BackendPageModulePreviewContentEventListener
{
    private string $templatePath = 'EXT:powermail_cleaner/Resources/Private/';

    public function __construct(
        private readonly ViewFactoryInterface $viewFactory,
        private readonly BackendConfigurationManager $backendConfigurationManager,
    ) {}

    public function __invoke(BackendPageModulePreviewContentEvent $event): void
    {
        $existingPreview = $event->getPreview();
        $record = $event->getItem()->getRecord();
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);

        if ($record['l18n_parent'] === 0) {
            $flexforms = $flexFormService->convertFlexFormContentToArray($record['pi_flexform']);
        } else {
            $flexforms = $flexFormService->convertFlexFormContentToArray(
                BackendUtility::getL18nParentFlexForm($record['l18n_parent']),
            );
        }

        if (ArrayUtility::isValidPath($flexforms, 'settings/flexform/powermailCleaner')) {
            $cleanerSettings = $flexforms['settings']['flexform']['powermailCleaner'];
            $cleanerSettings['optin'] = $flexforms['settings']['flexform']['main']['optin'];

            $event->setPreview(
                $existingPreview . $this->getCleanerPreview($cleanerSettings),
            );
        }
    }

    protected function getCleanerPreview(array $cleanerConfiguration): string
    {
        $request = $this->getRequest();

        $viewFactoryData = new ViewFactoryData(
            templateRootPaths: [$this->templatePath . 'Templates/'],
            partialRootPaths: [$this->templatePath . 'Partials/'],
            layoutRootPaths: [$this->templatePath . 'Layouts/'],
            request: $request,
        );

        $view = $this->viewFactory->create($viewFactoryData);
        $view->assignMultiple(
            [
                'deletionBehavior' => $cleanerConfiguration['deletionBehavior'],
                'deletionDate' => $cleanerConfiguration['deletionDate'] ?? '',
                'deletionPeriod' => $cleanerConfiguration['deletionPeriod'] ?? '',
                'informReceiversBeforeDeletion' => $cleanerConfiguration['informReceiversBeforeDeletion'] ?? '',
                'informReceiversBeforeDeletionPeriod' => $cleanerConfiguration['informReceiversBeforeDeletionPeriod'] ?? '',
            ]
        );

        if ('dbDisable' === $cleanerConfiguration['deletionBehavior'] && $cleanerConfiguration['optin'] === '1') {
            $view->assign('optinVsDbDelete', true);
        } elseif ('dbDisable' === $cleanerConfiguration['deletionBehavior']) {
            $typoScript = $this->backendConfigurationManager->getTypoScriptSetup($request);
            if ($typoScript['plugin.']['tx_powermail.']['settings.']['setup.']['main.']['optin'] === '1') {
                $view->assign('optinVsDbDelete', true);
            }
        }

        return $view->render('PluginPreview');
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
