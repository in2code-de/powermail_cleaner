<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\Domain\Repository;

use Doctrine\DBAL\Exception;
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
}