<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\EventListener;

use In2code\Powermail\Utility\HashUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;

final class FormControllerCreateActionBeforeRenderViewEventListener
{
    public function addTimestamp($mail, $hash, $plugin): void
    {
        $mail->setPlugin($plugin->getContentObject()->data['uid']);
        $mail->setDeletionTimestamp($this->calculateDeletionTimeStamp($plugin));

        if ($hash !== '') {
            $hash = (HashUtility::getHash($mail));
        }
    }

    private function calculateDeletionTimeStamp($plugin): int
    {
        $settings = $plugin->getSettings();
        if (ArrayUtility::isValidPath($settings, 'powermailCleaner')) {
            switch ($settings['powermailCleaner']['deletionBehavior']) {
                case 'deletionPeriod':
                    return $this->calculateDeletionTimeStampForPeriod(
                        (int)$settings['powermailCleaner']['deletionPeriod']
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

    private function calculateDeletionTimeStampForPeriod(int $deletionPeriod): int
    {
        return time() + $deletionPeriod * 86400;
    }
}
