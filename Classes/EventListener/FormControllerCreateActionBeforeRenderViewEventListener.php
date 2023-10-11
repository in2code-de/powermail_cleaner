<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\EventListener;

use In2code\Powermail\Events\FormControllerCreateActionBeforeRenderViewEvent;
use In2code\Powermail\Utility\HashUtility;
use In2code\PowermailCleaner\Controller\FormController;
use In2code\PowermailCleaner\Domain\Model\Mail;

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
        $event->setMail($mail);

        if ($hash !== '') {
            $event->setHash(HashUtility::getHash($mail));
        }
    }
}
