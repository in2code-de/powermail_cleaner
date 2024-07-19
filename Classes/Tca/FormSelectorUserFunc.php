<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Tca;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class FormSelectorUserFunc
 * shows forms in FlexForm (selection can be filtered via Page TSConfig)
 */
class FormSelectorUserFunc
{

    /**
     * Gets the l18n_parent's value of the field `settings.powermailCleaner.deletionBehavior`
     * and sets the dummy field to this value
     *
     * @param array $params
     *
     * @return void
     */
    public function getDeletionBehaviorReadonlyItems(array &$params): void
    {
        // Don't handle records without l18n parent
        if (empty($params['flexParentDatabaseRow']['l18n_parent'])) {
            return;
        }

        $params['items'] = [];

        // get L18n parent record
        $deletionBehavior = $params['row']['settings.flexform.powermailCleaner.deletionBehavior'];
        $deletionBehavior = (empty($deletionBehavior)) ? 0 : $deletionBehavior;
        $key              = 'LLL:EXT:powermail_cleaner/Resources/Private/Language/locallang_db:deletionBehavior.'
                            . $deletionBehavior;
        $params['items'][] = [
            LocalizationUtility::translate($key),
            '',
        ];
    }

    /**
     * Gets the l18n_parent's value of the field `settings.powermailCleaner.deletionPeriod`
     * and sets the dummy field to this value
     *
     * @param array $params
     *
     * @return void
     */
    public function getDeletionPeriodReadonlyItems(array &$params): void
    {
        // Don't handle records without l18n parent
        if (empty($params['flexParentDatabaseRow']['l18n_parent'])) {
            return;
        }

        $params['items'] = [];

        // get L18n parent record
        $deletionPeriod = $params['row']['settings.flexform.powermailCleaner.deletionPeriod'];
        $params['items'][] = [
            $deletionPeriod,
            '',
        ];
    }

    /**
     * Gets the l18n_parent's value of the field `settings.powermailCleaner.deletionDate`
     * and sets the dummy field to this value
     *
     * @param array $params
     *
     * @return void
     */
    public function getDeletionDateReadonlyItems(array &$params): void
    {
        // Don't handle records without l18n parent
        if (empty($params['flexParentDatabaseRow']['l18n_parent'])) {
            return;
        }

        $params['items'] = [];

        // get L18n parent record
        $deletionDate = $params['row']['settings.flexform.powermailCleaner.deletionDate'];
        $params['items'][] = [
            date('d-m-Y', (int)$deletionDate),
            '',
        ];
    }
}
