<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Hooks;

use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\PowermailCleaner\Utility\CalculateDeletionTimeStampUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Class UpdateDeletionTimeStampHook
 * Update DeletionTimeStamp in mails after re-configuring Plug-in settings
 */
class UpdateDeletionTimeStampHook
{

    /**
     * Hook for updating deletion timestamp in Mails
     * after updating deletionBehavior settings in Plug-in
     *
     * @param string $status
     * @param string $table
     * @param        $id
     * @param        $fieldArray
     *
     * @return void
     */
    public function processDatamap_postProcessFieldArray(string $status, string $table, $id, $fieldArray): void
    {
        // Check: Record has to be content with updated flexform values
        if ($table !== 'tt_content' || $status !== 'update' || empty($fieldArray['pi_flexform'])) {
            return;
        }
        // Check: Record has to be a powermail plugin
        $record = BackendUtility::getRecord($table, (int)$id);
        if ($record['CType'] !== 'list' || $record['list_type'] !== 'powermail_pi1') {
            return;
        }

        // Get FlexForm settings and calculate new deletion timestamp
        $flexFormService   = GeneralUtility::makeInstance(FlexFormService::class);
        $settings          = $flexFormService->convertFlexFormContentToArray($fieldArray['pi_flexform']);
        $flexformConfig    = $settings['settings']['flexform'];
        $deletionTimespamp = CalculateDeletionTimeStampUtility::calculateDeletionTimeStamp($flexformConfig);

        // Find and update related mails
        $mailRepository     = GeneralUtility::makeInstance(MailRepository::class);
        $mailsToUpdate      = $mailRepository->findByPlugin($id);
        $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        foreach ($mailsToUpdate as $mail) {
            /** @var \In2code\PowermailCleaner\Domain\Model\Mail $mail */
            if ($flexformConfig['powermailCleaner']['deletionBehavior'] === 'deletionPeriod') {
                // Override deletion timestamp based on crdate
                /** @var \DateTime $deletionTimespamp */
                $deletionTimespamp = CalculateDeletionTimeStampUtility::calculateDeletionTimeStamp(
                    $flexformConfig,
                    $mail->getCrdate()->getTimestamp()
                );
            }
            $mail->setDeletionTimestamp($deletionTimespamp);
            $persistenceManager->add($mail);
            $persistenceManager->persistAll();
        }
    }
}
