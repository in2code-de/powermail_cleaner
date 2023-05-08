<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FlexFormHook
 * Add PowermailCleaner fields in Flexform
 */
class FlexFormHook
{

    /**
     * Add PowermailCleaner fields in Flexform
     *
     * @param array $dataStructure
     * @param array $identifier
     *
     * @return array
     */
    public function parseDataStructureByIdentifierPostProcess(array $dataStructure, array $identifier): array
    {
        if ($identifier['type'] === 'tca'
            && $identifier['tableName'] === 'tt_content'
            && $identifier['dataStructureKey'] === 'powermail_pi1,list')
        {
            /** @noinspection Annotator */
            $filename = 'EXT:powermail_cleaner/Configuration/Flexforms/PowermailCleaner.xml';
            $file     = GeneralUtility::getFileAbsFileName($filename);
            $content  = file_get_contents($file);
            if ($content) {
                $dataStructure['sheets']['powermailCleaner'] = GeneralUtility::xml2array($content);
            }
        }

        return $dataStructure;
    }
}
