<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\Domain\Service;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CleanupService
{
    /**
     * @var Connection|null
     */
    protected $connection = null;

    public function cleanup($formId, $pluginId, $cleanupConfiguration)
    {
        switch ($cleanupConfiguration['deletionBehavior']) {
            case 'dbDisable':
                $this->dbDisable($formId, $pluginId);
                break;
            case 'deletionPeriod':
                $this->deletionPeriod($formId, $pluginId, $cleanupConfiguration);
                break;
            case 'deletionDate':
                $this->deletionDate($formId, $pluginId, $cleanupConfiguration);
                break;
        }
    }

    /**
     * Method to handle the "never save to database" deletion behavior
     *
     * @param $formId
     * @param $pluginId
     * @return void
     */
    protected function dbDisable($formId, $pluginId)
    {
        $mails = $this->getMailsWithCrdate($formId, $pluginId);

        $mails = array_map(function ($mail) {
            return $mail['uid'];
        }, $mails);

        $this->deleteMails($mails);
    }

    /**
     * Method to handle the "delete mails after period" deletion behavior
     *
     * @param $formId
     * @param $pluginId
     * @param $cleanupConfiguration
     * @return void
     */
    protected function deletionPeriod($formId, $pluginId, $cleanupConfiguration)
    {
        $mails = $this->getMailsWithCrdate($formId, $pluginId);

        $mails = array_filter($mails, function ($mail) use ($cleanupConfiguration) {
            return $mail['crdate'] < (time() - ((int)$cleanupConfiguration['deletionPeriod'] * 86400));
        });

        $mails = array_map(function ($mail) {
            return $mail['uid'];
        }, $mails);

        $this->deleteMails($mails);
    }

    /**
     * Method to handle the "delete mails older than date" deletion behavior
     *
     * @param $formId
     * @param $pluginId
     * @param $cleanupConfiguration
     * @return void
     */
    protected function deletionDate($formId, $pluginId, $cleanupConfiguration)
    {
        $mails = $this->getMailsWithCrdate($formId, $pluginId);

        // check if deletion date is in the future
        if ((int)$cleanupConfiguration['deletionDate'] > time()) {
            return;
        }

        $mails = array_filter($mails, function ($mail) use ($cleanupConfiguration) {
            return $mail['crdate'] < (int)$cleanupConfiguration['deletionDate'];
        });

        $mails = array_map(function ($mail) {
            return $mail['uid'];
        }, $mails);

        $this->deleteMails($mails);
    }

    protected function getMailsWithCrdate($formId, $pluginId)
    {
        $connection = $this->getConnection();

        $queryBuilder = $connection->createQueryBuilder();
        $mails = $queryBuilder->select('uid', 'crdate')
            ->from('tx_powermail_domain_model_mail')
            ->where($queryBuilder->expr()->eq('form', $queryBuilder->createNamedParameter($formId)))
            ->andWhere($queryBuilder->expr()->eq('plugin', $queryBuilder->createNamedParameter($pluginId)))
            ->execute()
            ->fetchAll();

        return $mails;
    }

    protected function deleteMails($uidList)
    {
        $connection = $this->getConnection();

        if (empty($uidList)) {
            return;
        }

        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder
            ->update('tx_powermail_domain_model_mail')
            ->set('deleted', 1)
            ->where($queryBuilder->expr()->in('uid', $uidList))
            ->execute();
    }

    protected function getConnection() {
        if ($this->connection == null) {
            $this->connection = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_powermail_domain_model_mail');
        }

        return $this->connection;
    }
}
