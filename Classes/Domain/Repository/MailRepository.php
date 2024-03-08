<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\Domain\Repository;

use Doctrine\DBAL\Exception;
use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\File;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\DatabaseUtility;
use In2code\PowermailCleaner\Utility\ConfigurationUtility;
use In2code\PowermailCleaner\Utility\FileUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class MailRepository extends \In2code\Powermail\Domain\Repository\MailRepository
{
    /**
     * @throws InvalidQueryException
     */
    public function findAllDeletionTimeStampOlderThan(int $timestamp): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $and = [
            $query->lessThan('deletion_timestamp', $timestamp),
            $query->greaterThan('deletion_timestamp', 0),
        ];
        $query->matching($query->logicalAnd(...$and));
        return $query->execute();
    }

    /**
     * @throws Exception
     */
    public function countMailsForPluginAndTranslatedWithinTimeframe(int $pluginUid, array $notificationLimit): int
    {
        $count = $this->countMailsForPlugin($pluginUid, $notificationLimit);

        $translatedPlugins = $this->getTranslatedPlugins($pluginUid);
        foreach ($translatedPlugins as $translatedPlugin) {
            $count =+ $this->countMailsForPlugin($translatedPlugin['uid'], $notificationLimit);
        }

        return $count;
    }

    private static function getQueryBuilderForTable(string $tableName, bool $removeRestrictions = false): QueryBuilder
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
        if ($removeRestrictions === true) {
            $queryBuilder->getRestrictions()->removeAll();
        }
        return $queryBuilder;
    }

    /**
     * @throws Exception
     */
    private function getTranslatedPlugins(int $pluginUid): array
    {
        $queryBuilder = self::getQueryBuilderForTable('tt_content');
        return $queryBuilder
            ->select('uid', 'pid', 'header as content_header', 'pi_flexform', 'l18n_parent')
            ->from('tt_content')
            ->where('CType = "powermail_pi1" ')
            ->andWhere('l18n_parent = ' . $queryBuilder->createNamedParameter($pluginUid))
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @throws InvalidQueryException
     */
    private function countMailsForPlugin(int $pluginUid, array $notificationLimit): int
    {
        $query = $this->createQuery();
        $and = [
            $query->equals('plugin', $pluginUid),
            $query->greaterThanOrEqual('deletion_timestamp', $notificationLimit['start']),
            $query->lessThan('deletion_timestamp', $notificationLimit['end']),
        ];
        return $query->matching($query->logicalAnd(...$and))->count();
    }

    public function removeMail(int $mailIdentifier): void
    {
        $answersWithFiles = $this->getAnswersWithFiles($mailIdentifier);
        $deleteReferencedFiles = ConfigurationUtility::getExtensionConfiguration()['deleteReferencedFiles'];

        if (count($answersWithFiles) > 0) {
            $uploadFolder = ConfigurationUtility::getPowermailUploadFolder();
            $storage = FileUtility::getStorage($uploadFolder);

            foreach ($answersWithFiles as $answer) {
                $filesInformation = FileUtility::getFilesInformation($answer['value'], $uploadFolder, $storage);
                foreach ($filesInformation as $file) {
                    if ($deleteReferencedFiles === '0' && FileUtility::hasSysfileReference($file['uid']) === true) {
                        break;
                    }
                    if ($file['uid'] !== false) {
                        FileUtility::deleteSysFileProcessedfile($file['uid'], $storage);
                        FileUtility::deleteSysFileReference($file['uid']);
                        FileUtility::deleteSysFile($file, $storage);
                    }
                    FileUtility::deleteFromFilesystem($file['identifier'], $storage);
                }
            }
        }

        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Answer::TABLE_NAME);
        $queryBuilder
            ->delete(Answer::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'mail',
                    $queryBuilder->createNamedParameter($mailIdentifier, Connection::PARAM_INT)
                )
            )
            ->executeStatement();

        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Mail::TABLE_NAME);
        $queryBuilder
            ->delete(Mail::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($mailIdentifier, Connection::PARAM_INT)
                )
            )
            ->executeStatement();
    }

    public function findMailsOlderThan(int $timestamp): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Mail::TABLE_NAME);
        $queryBuilder->getRestrictions()->removeAll();
        return $queryBuilder
            ->select('*')
            ->from(Mail::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->lt(
                    'crdate',
                    $queryBuilder->createNamedParameter($timestamp, Connection::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @param int $mailIdentifier
     * @return QueryBuilder
     * @throws Exception
     */
    public function getAnswersWithFiles(int $mailIdentifier): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Answer::TABLE_NAME);
        return $queryBuilder
            ->select('uid', 'value')
            ->from(Answer::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'mail',
                    $queryBuilder->createNamedParameter($mailIdentifier, Connection::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'value_type',
                    $queryBuilder->createNamedParameter(Answer::VALUE_TYPE_UPLOAD, Connection::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

}
