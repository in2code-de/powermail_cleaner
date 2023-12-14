<?php

declare(strict_types=1);
namespace In2code\PowermailCleaner\Utility;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class DatabaseUtility
 * @codeCoverageIgnore
 */
class DatabaseUtility
{
    public static function getPowermailPi1Plugins(): array
    {
        $queryBuilder = self::getQueryBuilderForTable('tt_content');
        $powermailPlugins  = $queryBuilder
            ->select(
                'tt_content.uid as content_uid',
                'tt_content.pid',
                'tt_content.header as content_header',
                'tt_content.pi_flexform',
                'tt_content.l18n_parent',
                'p.uid as page_uid',
                'p.title as page_title'
            )
            ->from('tt_content')
            ->join(
                'tt_content',
                'pages',
                'p',
                $queryBuilder->expr()->eq('p.uid', $queryBuilder->quoteIdentifier('tt_content.pid'))
            )
            ->where('tt_content.CType = "powermail_pi1" ')
            ->andWhere('tt_content.l18n_parent = 0')
            ->executeQuery()
            ->fetchAllAssociative();

        return $powermailPlugins;
    }

    public static function getQueryBuilderForTable(string $tableName, bool $removeRestrictions = false): QueryBuilder
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
        if ($removeRestrictions === true) {
            $queryBuilder->getRestrictions()->removeAll();
        }
        return $queryBuilder;
    }
}
