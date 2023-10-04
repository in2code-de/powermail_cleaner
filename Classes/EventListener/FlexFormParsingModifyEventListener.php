<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\EventListener;

use In2code\Powermail\Utility\DatabaseUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class FlexFormParsingModifyEventListener
{
    public function __invoke(AfterFlexFormDataStructureParsedEvent $event): void
    {
        $identifier = $event->getIdentifier();

        if (($identifier['dataStructureKey'] ?? '') === '*,powermail_pi1') {
            $parsedDataStructure = $event->getDataStructure();
            /** @noinspection Annotator */
            $filename = 'EXT:powermail_cleaner/Configuration/FlexForms/PowermailCleaner.xml';
            $file     = GeneralUtility::getFileAbsFileName($filename);
            $content  = file_get_contents($file);
            if ($content) {
                $parsedDataStructure['sheets']['powermailCleaner'] = GeneralUtility::xml2array($content);
            }
            $event->setDataStructure($parsedDataStructure);
        }
    }

    /**
     * Get field configuration from page TSconfig
     *
     * @param int $pid Record pid
     * @return array
     */
    protected function getFieldConfiguration(): array
    {
        $pid = $this->getPidForCurrentRecord();

        $tsConfiguration = BackendUtility::getPagesTSconfig((int)$pid);
        if (!empty($tsConfiguration['tx_powermail.']['flexForm.']['addField.'])) {
            $eConfiguration = $tsConfiguration['tx_powermail.']['flexForm.']['addField.'];
            $tsService = GeneralUtility::makeInstance(TypoScriptService::class);
            return $tsService->convertTypoScriptArrayToPlainArray($eConfiguration);
        }
        return [];
    }

    /**
     * Get sheetname and remove from configuration array
     *
     * @param array $configuration
     * @return string
     */
    protected function getSheetNameAndRemoveFromConfiguration(array &$configuration): string
    {
        $sheet = $this->allowedSheets[0];
        if (!empty($configuration['_sheet']) && in_array($configuration['_sheet'], $this->allowedSheets)) {
            $sheet = $configuration['_sheet'];
        }
        unset($configuration['_sheet']);
        return $sheet;
    }

    private function getPidForCurrentRecord(): int
    {
        $request = $this->getRequest();
        $queryParams = $request->getQueryParams();
        $uid = array_keys($queryParams['edit']['tt_content'] ?? [])[0] ?? 0;
        return DatabaseUtility::getPidForRecord($uid, 'tt_content');
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
