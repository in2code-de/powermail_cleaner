<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class MailRepository extends \In2code\Powermail\Domain\Repository\MailRepository
{
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
}