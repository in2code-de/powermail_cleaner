<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\EventListener;

use In2code\Powermail\Utility\HashUtility;
use In2code\PowermailCleaner\Utility\CalculateDeletionTimeStampUtility;

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
        return CalculateDeletionTimeStampUtility::calculateDeletionTimeStamp($settings);
    }
}
