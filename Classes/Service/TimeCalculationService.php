<?php

namespace In2code\PowermailCleaner\Service;

class TimeCalculationService
{

    public function calculateNotificationTimeframe(int $informReceiversBeforeDeletionPeriod): array
    {
        $notificationLimit = [];
        $beginOfDay = strtotime('today +' . $informReceiversBeforeDeletionPeriod . 'days', time());
        $notificationLimit['start'] = strtotime('today', $beginOfDay);
        $notificationLimit['end'] = $notificationLimit['start'] + 86400;

        return $notificationLimit;
    }

    /**
     * @todo Remove? Is not used
     */
    private static function getTodaysStart(): int
    {
        return strtotime('today', time());
    }
}
