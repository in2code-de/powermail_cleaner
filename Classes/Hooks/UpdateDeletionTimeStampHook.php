<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Hooks;

use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\PowermailCleaner\Utility\CalculateDeletionTimeStampUtility;
use PDO;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\WorkspaceRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Class UpdateDeletionTimeStampHook
 * Update DeletionTimeStamp in mails after re-configuring Plug-in settings
 */
class UpdateDeletionTimeStampHook
{
    protected ?FlexFormService $flexFormService = null;
    protected array            $flexformConfig  = [];

    /**
     * Hook for updating deletion timestamp in Mails after updating deletionBehavior settings in Plug-in
     * and for synchronizing deletion settings in localized Plug-ins
     *
     * @param string                                   $status
     * @param string                                   $table
     * @param                                          $id
     * @param                                          $fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     *
     * @return void
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function processDatamap_postProcessFieldArray(
        string $status,
        string $table,
        $id,
        $fieldArray,
        DataHandler $dataHandler
    ): void
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
        $this->calculateDeletionTimeStamp($id, $fieldArray);

        // Synchronize deletion settings in localized records
        $this->synchronizeLocalizedRecords((int)$id, $dataHandler);
    }

    /**
     * Get FlexForm settings and calculate new deletion timestamp
     *
     * @param $id
     * @param $fieldArray
     *
     * @return void
     */
    protected function calculateDeletionTimeStamp($id, $fieldArray): void
    {
        $this->flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $settings              = $this->flexFormService->convertFlexFormContentToArray($fieldArray['pi_flexform']);
        $this->flexformConfig  = $settings['settings']['flexform'];
        $deletionTimespamp     = CalculateDeletionTimeStampUtility::calculateDeletionTimeStamp($this->flexformConfig);

        // Find and update related mails
        $mailRepository     = GeneralUtility::makeInstance(MailRepository::class);
        $mailsToUpdate      = $mailRepository->findByPlugin($id);
        $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        foreach ($mailsToUpdate as $mail) {
            /** @var \In2code\PowermailCleaner\Domain\Model\Mail $mail */
            if ($this->flexformConfig['powermailCleaner']['deletionBehavior'] === 'deletionPeriod') {
                // Override deletion timestamp based on crdate
                /** @var \DateTime $deletionTimespamp */
                $deletionTimespamp = CalculateDeletionTimeStampUtility::calculateDeletionTimeStamp(
                    $this->flexformConfig,
                    $mail->getCrdate()->getTimestamp()
                );
            }
            $mail->setDeletionTimestamp($deletionTimespamp);
            $persistenceManager->add($mail);
            $persistenceManager->persistAll();
        }
    }

    /**
     * Synchronize deletion settings in localized records
     *
     * @param int                                      $uid
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     *
     * @return void
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function synchronizeLocalizedRecords(int $uid, DataHandler $dataHandler): void
    {
        // Fetches the localization for the plug-in in default language (l18n_parent)
        $recordLocalization = static::getRecordLocalization($uid);
        $countRecords       = count($recordLocalization);
        if ($countRecords > 0) {
            $flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);
            foreach ($recordLocalization as $record) {
                // Get FlexForm config of localized record
                $piFlexform  = GeneralUtility::xml2array($record['pi_flexform']);
                // Override config with settings from l18n_parent
                $cleanerConf =& $piFlexform['data']['powermailCleaner']['lDEF'];
                foreach (['deletionBehavior', 'deletionDate', 'deletionPeriod'] as $field) {
                    $cleanerConf['settings.flexform.powermailCleaner.' . $field]['vDEF']
                        = $this->flexformConfig['powermailCleaner'][$field];
                }
                // Convert array back to flexform XML
                $piFlexform = $flexFormTools->flexArray2Xml($piFlexform, true);
                // Update localized record
                $dataHandler->updateDB('tt_content', (int)$record['uid'], ['pi_flexform' => $piFlexform]);
            }
        }
    }

    /**
     * Fetches the localization for a given record.
     *
     * @param int $uid The uid of the record
     *
     * @return mixed Multidimensional array with selected records, empty array if none exists and FALSE if table is not
     *               localizable
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Driver\Exception
     * @see BackendUtility::getRecordLocalization()
     */
    protected static function getRecordLocalization(int $uid): mixed
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder
            ->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
            ->add(GeneralUtility::makeInstance(
                WorkspaceRestriction::class,
                static::getBackendUserAuthentication()->workspace ?? 0
            ))
        ;

        $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'l18n_parent',
                    $queryBuilder->createNamedParameter($uid, PDO::PARAM_INT)
                )
            )
        ;

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    /**
     * @return BackendUserAuthentication|null
     * @see BackendUtility::getBackendUserAuthentication(), which is protected
     */
    protected static function getBackendUserAuthentication(): ?BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'] ?? null;
    }
}
