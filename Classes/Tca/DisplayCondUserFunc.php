<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Tca;

use In2code\PowermailCleaner\Utility\BackendUtility;

/**
 * Class FormSelectorUserFunc
 * DisplayConds for dependend fields in translated records
 */
class DisplayCondUserFunc
{

    /**
     * DisplayCond for field `deletionPeriod_l10n`
     *
     * @param array $params
     *
     * @return bool
     */
    public static function getDeletionPeriodCondition(array $params): bool
    {
        return self::getFieldCondition($params, 'deletionPeriod');
    }

    /**
     * DisplayCondfor field `deletionDate_l10n`
     *
     * @param array $params
     *
     * @return bool
     */
    public static function getDeletionDateCondition(array $params): bool
    {
        return self::getFieldCondition($params, 'deletionDate');
    }

    /**
     * Helper (common code) for DisplayCond methods
     *
     * @param array  $params
     * @param string $field
     *
     * @return bool
     */
    protected static function getFieldCondition(array $params, string $field): bool
    {
        // Don't handle records without l18n parent
        if (empty($params['record']['l18n_parent'][0])) {
            return true;
        }

        // get L18n parent record
        $deletionBehavior = BackendUtility::getL18nParentFlexformFieldData(
            (int)$params['record']['l18n_parent'][0],
            'deletionBehavior'
        );

        return ($deletionBehavior === $field);
    }


    /**
     * DisplayCondfor field `deletionContact`
     *
     * @param array $params
     *
     * @return bool
     */
    public static function getDeletionContactCondition(array $params): bool
    {
        if (empty($params['record']['l18n_parent'][0])) {
            $keyRecord = $params['record']['uid'];
        } else {
            $keyRecord = $params['record']['l18n_parent'][0];
        }
        $deletionBehavior = BackendUtility::getL18nParentFlexformFieldData((int)$keyRecord, 'deletionBehavior');

        return ($deletionBehavior === 'deletionPeriod' OR $deletionBehavior === 'deletionDate');
    }

    public static function getInformReceiversBeforeDeletionCondition(array $params): bool
    {
        if (empty($params['record']['l18n_parent'][0])) {
            $keyRecord = $params['record']['uid'];
        } else {
            $keyRecord = $params['record']['l18n_parent'][0];
        }

        $informReceiversBeforeDeletion = BackendUtility::getL18nParentFlexformFieldData(
            (int)$keyRecord,
            'informReceiversBeforeDeletion'
        );
        $deletionBehavior = BackendUtility::getL18nParentFlexformFieldData((int)$keyRecord, 'deletionBehavior');

        return (
            !empty($informReceiversBeforeDeletion)
            && ($deletionBehavior === 'deletionPeriod' || $deletionBehavior === 'deletionDate')
        );
    }

    /**
     * DisplayCondfor field `deletionDetails`
     *
     * @param array $params
     *
     * @return bool
     */
    public static function getDeletionDetailsCondition(array $params): bool
    {
        if (empty($params['record']['l18n_parent'][0])) {
            $keyRecord = $params['record']['uid'];
        } else {
            $keyRecord = $params['record']['l18n_parent'][0];
        }
        $deletionBehavior = BackendUtility::getL18nParentFlexformFieldData((int)$keyRecord, 'deletionBehavior');

        return (!empty($deletionBehavior));
    }
}
