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
    protected ?Connection $connectionForMails = null;

    protected ?Connection $connectionForAnswers = null;

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
        $connectionForMails = $this->getConnectionForMails();

        $queryBuilder = $connectionForMails->createQueryBuilder();
        return $queryBuilder->select('uid', 'crdate')
            ->from('tx_powermail_domain_model_mail')
            ->where($queryBuilder->expr()->eq('form', $queryBuilder->createNamedParameter($formId)))
            ->andWhere($queryBuilder->expr()->eq('plugin', $queryBuilder->createNamedParameter($pluginId)))
            ->executeQuery()
            ->fetchAllAssociative();
    }

    protected function deleteMails($uidList)
    {
        $connectionForMails = $this->getConnectionForMails();
        $connectionForAnswers = $this->getConnectionForMails();

        if (empty($uidList)) {
            return;
        }

        $queryBuilder = $connectionForMails->createQueryBuilder();
        $queryBuilder
            ->update('tx_powermail_domain_model_mail')
            ->set('deleted', 1)
            ->where($queryBuilder->expr()->in('uid', $uidList))
            ->executeStatement();

        $queryBuilder = $connectionForAnswers->createQueryBuilder();
        $queryBuilder
            ->update('tx_powermail_domain_model_answer')
            ->set('deleted',1)
            ->where($queryBuilder->expr()->in('mail', $uidList))
            ->executeStatement();

    }

    protected function getConnectionForMails() {
        if ($this->connectionForMails == null) {
            $this->connectionForMails = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_powermail_domain_model_mail');
        }

        return $this->connectionForMails;
    }

    protected function getConnectionForAnswers() {
        if ($this->connectionForAnswers == null) {
            $this->connectionForAnswers = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_powermail_domain_model_answer');
        }

        return $this->connectionForAnswers;
    }
}
