<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\EventListener;

use In2code\Powermail\Events\FormControllerCreateActionBeforeRenderViewEvent;
use In2code\Powermail\Utility\HashUtility;
use In2code\PowermailCleaner\Controller\FormController;
use In2code\PowermailCleaner\Domain\Model\Mail;
use TYPO3\CMS\Core\Utility\ArrayUtility;

final class FormControllerCreateActionBeforeRenderViewEventListener
{
    public function __invoke(FormControllerCreateActionBeforeRenderViewEvent $event): void
    {
        /** @var FormController $controller */
        $controller = $event->getFormController();
        /** @var Mail $mail */
        $mail = $event->getMail();
        $hash = $event->getHash();

        $mail->setPlugin($controller->getContentObject()->data['uid']);
        $mail->setDeletionTimestamp($this->calculateDeletionTimeStamp($controller));

        $event->setMail($mail);

        if ($hash !== '') {
            $event->setHash(HashUtility::getHash($mail));
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
