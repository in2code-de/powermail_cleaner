<?php
declare(strict_types = 1);
namespace In2code\PowermailCleaner\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class BackendUtility
 */
class BackendUtility
{

    /**
     * Get Flexform Data from L18n Parent Record
     *
     * @param int    $l18nParent
     * @param string $field
     *
     * @return string
     */
    public static function getL18nParentFlexformFieldData(int $l18nParent, string $field): string
    {
        $l18nParent   = BackendUtilityCore::getRecord('tt_content', $l18nParent, 'pi_flexform');
        $flexFormData = (isset($l18nParent['pi_flexform'])) ? GeneralUtility::xml2array($l18nParent['pi_flexform']) : [];
        $flexFormData = $flexFormData['data']['powermailCleaner']['lDEF'] ?? [];

        if (empty($flexFormData['settings.flexform.powermailCleaner.' . $field]['vDEF'])) {
            return '';
        }

        return $flexFormData['settings.flexform.powermailCleaner.' . $field]['vDEF'];
    }

    public static function getL18nParentFlexForm(int $l18nParent): string
    {
        $l18nParentRecord = BackendUtilityCore::getRecord('tt_content', $l18nParent, 'pi_flexform');
        return $l18nParentRecord['pi_flexform'];
    }
}
