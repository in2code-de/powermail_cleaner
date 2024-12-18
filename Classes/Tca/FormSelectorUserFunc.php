<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Tca;

use In2code\PowermailCleaner\Utility\BackendUtility;
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
    public function getDeletionBehaviorL10nItems(array &$params): void
    {
        // Don't handle records without l18n parent
        if (empty($params['flexParentDatabaseRow']['l18n_parent'])) {
            return;
        }

        $params['items'] = [];

        // get L18n parent record
        $deletionBehavior = BackendUtility::getL18nParentFlexformFieldData(
            (int)$params['flexParentDatabaseRow']['l18n_parent'],
            'deletionBehavior'
        );
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
    public function getDeletionPeriodL10nItems(array &$params): void
    {
        // Don't handle records without l18n parent
        if (empty($params['flexParentDatabaseRow']['l18n_parent'])) {
            return;
        }

        $params['items'] = [];

        // get L18n parent record
        $deletionPeriod = BackendUtility::getL18nParentFlexformFieldData(
            (int)$params['flexParentDatabaseRow']['l18n_parent'],
            'deletionPeriod'
        );
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
    public function getDeletionDateL10nItems(array &$params): void
    {
        // Don't handle records without l18n parent
        if (empty($params['flexParentDatabaseRow']['l18n_parent'])) {
            return;
        }

        $params['items'] = [];

        // get L18n parent record
        $deletionDate = BackendUtility::getL18nParentFlexformFieldData(
            (int)$params['flexParentDatabaseRow']['l18n_parent'],
            'deletionDate'
        );
        $params['items'][] = [
            date('d-m-Y', (int)$deletionDate),
            '',
        ];
    }

    public function getInformReceiversBeforeDeletionL10nItem(array &$params): void
    {
        // Don't handle records without l18n parent
        if (empty($params['flexParentDatabaseRow']['l18n_parent'])) {
            return;
        }

        $params['items'] = [];

        // get L18n parent record
        $informReceiversBeforeDeletion = BackendUtility::getL18nParentFlexformFieldData(
            (int)$params['flexParentDatabaseRow']['l18n_parent'],
            'informReceiversBeforeDeletion'
        );

        if (!empty($informReceiversBeforeDeletion)) {
            $params['items'][] = [
                'label' => 'LLL:EXT:powermail_cleaner/Resources/Private/Language/locallang_db.xlf:informReceiversBeforeDeletion.yes',
            ];
        } else {
            $params['items'][] = [
                'label' => 'LLL:EXT:powermail_cleaner/Resources/Private/Language/locallang_db.xlf:informReceiversBeforeDeletion.no',
            ];
        }
    }
    public function getInformReceiversBeforeDeletionPeriodL10nItem(array &$params): void
    {
        // Don't handle records without l18n parent
        if (empty($params['flexParentDatabaseRow']['l18n_parent'])) {
            return;
        }

        $params['items'] = [];

        // get L18n parent record
        $informReceiversBeforeDeletionPeriod = BackendUtility::getL18nParentFlexformFieldData(
            (int)$params['flexParentDatabaseRow']['l18n_parent'],
            'informReceiversBeforeDeletionPeriod'
        );

        $params['items'][] = [
            'label' => $informReceiversBeforeDeletionPeriod,
        ];
    }
}
