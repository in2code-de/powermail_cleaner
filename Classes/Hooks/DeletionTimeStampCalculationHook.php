<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\Hooks;

use In2code\Powermail\Utility\HashUtility;
use In2code\PowermailCleaner\Controller\FormController;
use In2code\PowermailCleaner\Domain\Model\Mail;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
