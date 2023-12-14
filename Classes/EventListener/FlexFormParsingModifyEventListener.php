<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\EventListener;

use TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent;
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
}
