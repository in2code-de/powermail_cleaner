<?php

namespace In2code\PowermailCleaner\Domain\Service;

use In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesService;

class ReceiverAddressService extends ReceiverMailReceiverPropertiesService
{
    protected function setReceiverEmails(): void
    {
        $emailArray = $this->getEmailsFromFlexForm();
        $emailArray = $this->getEmailsFromFeGroup($emailArray, (int)($this->settings['receiver']['fe_group'] ?? 0));
        $emailArray = $this->getEmailsFromBeGroup($emailArray, (int)($this->settings['receiver']['be_group'] ?? 0));
        $emailArray = $this->getEmailsFromPredefinedEmail(
            $emailArray,
            (string)($this->settings['receiver']['predefinedemail'] ?? '')
        );
        $this->receiverEmails = $this->getEmailFromDevelopmentContext($emailArray);
    }
}