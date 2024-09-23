<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Hooks;

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

/**
 * Class UpdateDeletionTimeStampHook
 * Hook for synchronizing deletion settings in localized Plug-ins
 */
class SynchronizeLocalizedRecordsHook
{

    protected string           $table           = 'tt_content';
    protected array            $flexformConfig  = [];
    protected ?DataHandler     $dataHandler     = null;
    protected array            $currentRecord   = [];
    protected array            $syncFields      = ['deletionBehavior', 'deletionDate', 'deletionPeriod'];

    /**
     * Synchronize deletion behavior between l18n_parent record and localized record
     *
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     *
     * @return void
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function processDatamap_afterAllOperations(DataHandler $dataHandler): void
    {
        // Check: Record has to be content
        if (empty($dataHandler->datamap[$this->table])) {
            return;
        }

        foreach ($dataHandler->datamap[$this->table] as $uid => $record) {
            // Check: Record has to be a powermail plugin
            if (!empty($record['CType']) && $record['CType'] === 'list'
                && !empty($record['list_type']) && $record['list_type'] === 'powermail_pi1') {
                $record['uid']       = (int)$uid;
                $this->currentRecord = $record;
            }
        }
        if (empty($this->currentRecord)) {
            return;
        }

        // Assign objects
        $this->dataHandler     = $dataHandler;

        if ((int)$this->currentRecord['sys_language_uid'] === 0 && empty($this->currentRecord['l18n_parent'])) {
            // Synchronize deletion behaviour settings to localized records
            $this->synchronizeLocalizedRecordToChildren();
        } else {
            // Synchronize deletion behaviour settings from parent
            $this->synchronizeLocalizedRecordFromParent();
        }
    }

    /**
     * Synchronize deletion behaviour settings to localized records
     *
     * @return void
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function synchronizeLocalizedRecordToChildren(): void
    {
        if (empty($this->currentRecord['pi_flexform']['data'])) {
            return;
        }

        $this->flexformConfig = $this->convertFlexFormArray($this->currentRecord['pi_flexform']['data']);

        // Fetches the localization for the plug-in in default language (l18n_parent)
        $recordLocalization = static::getRecordLocalization($this->currentRecord['uid']);
        $countRecords       = count($recordLocalization);
        if ($countRecords > 0) {
            foreach ($recordLocalization as $record) {
                $this->updateLocalizedRecord($record);
            }
        }
    }

    /**
     * Synchronize deletion behaviour settings from parent
     *
     * @return void
     */
    private function synchronizeLocalizedRecordFromParent(): void
    {
        // Get FlexForm config of l18n parent record
        $l18nParent = BackendUtility::getRecord($this->table, $this->currentRecord['l18n_parent']);
        if (is_null($l18nParent)) {
            return;
        }

        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        if (is_array($l18nParent['pi_flexform'])) {
            $settings = $flexFormService->convertFlexFormContentToArray($l18nParent['pi_flexform']);
            $this->flexformConfig = $settings['settings']['flexform']['powermailCleaner'];
            $this->updateLocalizedRecord($this->currentRecord);
        }
    }


    /**
     * Reformat Flexform Data
     * Input:
     * $flexformData = [
     *     'powermailCleaner' => [
     *         'lDEF' => [
     *             'settings.flexform.powermailCleaner.deletionBehavior' => [
     *                 'vDEF' => 'deletionPeriod',
     *             ],
     *             'settings.flexform.powermailCleaner.deletionPeriod'   => [
     *                 'vDEF' => '27',
     *             ],
     *         ],
     *     ],
     * ];
     *
     * Output:
     * $flexformConfig = [
     *     'deletionBehavior' => 'deletionPeriod',
     *     'deletionDate'     => null,
     *     'deletionPeriod'   => '27',
     * ];
     *
     * @param array $flexformData
     *
     * @return array
     */
    private function convertFlexFormArray(array $flexformData): array
    {
        $flexFormConfiguration = [];
        foreach ($this->syncFields as $field) {
            $flexFormConfiguration[$field]
                = $flexformData['powermailCleaner']['lDEF']['settings.flexform.powermailCleaner.' . $field]['vDEF'];
        }

        return $flexFormConfiguration;
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

    /**
     * Override deletion behaviour settings and save record with new value
     *
     * @param array $record
     *
     * @return void
     */
    protected function updateLocalizedRecord(array $record): void
    {
        // Get FlexForm config of localized record
        $piFlexform = (is_array($record['pi_flexform'])) ? $record['pi_flexform'] : GeneralUtility::xml2array(
            $record['pi_flexform']
        );
        // if the flexform is not filled, a string is returned, then we exit here
        if (is_array($piFlexform['data']['powermailCleaner']['lDEF'])) {
            $cleanerConf =& $piFlexform['data']['powermailCleaner']['lDEF'];
            // Override config with settings from l18n_parent
            foreach ($this->syncFields as $field) {
                $cleanerConf['settings.flexform.powermailCleaner.' . $field]['vDEF']
                    = $this->flexformConfig[$field];
            }
            // Convert array back to flexform XML
            $flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);
            $piFlexform = $flexFormTools->flexArray2Xml($piFlexform, true);
            // Update localized record
            $this->dataHandler->updateDB($this->table, (int)$record['uid'], ['pi_flexform' => $piFlexform]);
        }
    }
}
