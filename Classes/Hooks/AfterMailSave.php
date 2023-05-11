<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Hooks;

use In2code\Powermail\Controller\FormController;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AfterMailSave
{
    /**
     * @param $mail \In2code\PowermailCleaner\Domain\Model\Mail
     * @param $formController FormController
     * @return void
     */
    public function attachPlugin($mail, $formController)
    {
        /** @var \In2code\PowermailCleaner\Controller\FormController $formController */
        $pluginUid = $formController->getContentObject()->data['uid'];

        $mail->setPlugin($pluginUid);

        $persistenceManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
        $persistenceManager->add($mail);
        $persistenceManager->persistAll();
    }
}