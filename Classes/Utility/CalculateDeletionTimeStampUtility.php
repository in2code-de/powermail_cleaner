<?php
declare(strict_types = 1);
namespace In2code\PowermailCleaner\Utility;

use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Class CalculateDeletionTimeStampUtility
 */
class CalculateDeletionTimeStampUtility
{

    /**
     * Calculate deletion timestamp based on deletion period or deletion date
     *
     * @param array $settings
     * @param int   $overrideCrdate
     *
     * @return int
     */
    public static function calculateDeletionTimeStamp(array $settings, int $overrideCrdate = 0): int
    {
        if (ArrayUtility::isValidPath($settings, 'powermailCleaner')) {
            switch ($settings['powermailCleaner']['deletionBehavior']) {
                case 'deletionPeriod':
                    return self::calculateDeletionTimeStampForPeriod(
                        (int)$settings['powermailCleaner']['deletionPeriod'],
                        $overrideCrdate
                    );
                case 'deletionDate':
                    return (int)$settings['powermailCleaner']['deletionDate'];
                case 'dbDisable':
                    return -1;
                default:
                    return 0;
            }
        }

        return 0;
    }

    /**
     * Calculate deletion timestamp for a given deletion period
     *
     * @param int $deletionPeriod
     * @param int $overrideCrdate
     *
     * @return int
     */
    private static function calculateDeletionTimeStampForPeriod(int $deletionPeriod, int $overrideCrdate = 0): int
    {
        $time = !empty($overrideCrdate) ? $overrideCrdate : time();
        return $time + $deletionPeriod * 86400;
    }
}
