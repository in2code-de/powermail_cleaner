<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\Hooks;

use In2code\Powermail\Utility\HashUtility;
use In2code\PowermailCleaner\Controller\FormController;
use In2code\PowermailCleaner\Domain\Model\Mail;
use In2code\PowermailCleaner\Utility\CalculateDeletionTimeStampUtility;

final class DeletionTimeStampCalculationHook
{
    /**
     * @param Mail $mail
     * @param string $hash
     * @param FormController $formController
     * @return void
     * @throws \Exception
     */
    public function invoke($mail, $hash, $formController): void
    {
        $mail->setPlugin($formController->getContentObject()->data['uid']);
        $mail->setDeletionTimestamp($this->calculateDeletionTimeStamp($formController));

        if ($hash !== '') {
            $hash = HashUtility::getHash($mail);
        }
    }

    private function calculateDeletionTimeStamp(FormController $controller): int
    {
        $settings = $controller->getSettings();
        return CalculateDeletionTimeStampUtility::calculateDeletionTimeStamp($settings);
    }
}
