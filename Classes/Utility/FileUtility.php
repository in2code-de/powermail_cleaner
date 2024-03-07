<?php

namespace In2code\PowermailCleaner\Utility;

use TYPO3\CMS\Core\Configuration\SiteConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileUtility
{
    public static function getSysFileUids(string $answerValue, ResourceStorage $storage, string $uploadFolder): array
    {
        $sysFileUids = array();

        // strip leading and closing characters from
        $answerValue = str_replace(['["', '"]'], '', $answerValue);

        // split answer value into single filenames
        $files = GeneralUtility::trimExplode('","', $answerValue, true);
        foreach ($files as $file) {
            $identifier = self::buildIdentifier($file, $storage, $uploadFolder);
            $sysFileUid = self::getSysFileUid($storage->getUid(), $identifier);
            if ($sysFileUid !== false) {
                $sysFileUids[] = $sysFileUid;
            }
        }
        return $sysFileUids;
    }

    private static function buildIdentifier(string $filename, ResourceStorage $storage, string $uploadFolder)
    {
        $uploadFolderWithoutResourcePath = self::stripResourcePath($storage, $uploadFolder);
        $identifier = '/' . $uploadFolderWithoutResourcePath . '/' . $filename;
        return $identifier;

    }
    public static function getStorage(string $path): ?ResourceStorage
    {
        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        $allStorages = $storageRepository->findAll();

        foreach ($allStorages as $storage) {
            $config = $storage->getConfiguration();
            if (str_starts_with($path, $config['basePath'])) {
                return $storage;
            }
        }
        return null;
    }

    public static function hasSysfileReference(int $sysFileUid): bool
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable('sys_file_reference');
        $result = $queryBuilder->count('uid')
            ->from('sys_file_reference')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid_local',
                    $queryBuilder->createNamedParameter($sysFileUid, Connection::PARAM_INT)
                )
            )
            ->executeStatement();
        return $result > 0 ? true : false;
    }

    public static function deleteFromFilesystem(mixed $answer): void
    {
        // Delete from filesystem
        // File =  Uploadfolder + answervalue
    }

    public static function deleteSysfile(int $sysFileUid)
    {
        // delete filerecord from sys_file table
    }

    private static function stripResourcePath(ResourceStorage $storage, string $uploadFolder): string
    {
        return str_replace($storage->getConfiguration()['basePath'], '', $uploadFolder);
    }

    private static function getSysFileUid(int $storageUid, string $identifier): bool|int
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable('sys_file');
        $result = $queryBuilder
            ->select('uid')
            ->from('sys_file')
            ->where(
                $queryBuilder->expr()->eq(
                    'storage',
                    $queryBuilder->createNamedParameter($storageUid, Connection::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'identifier',
                    $queryBuilder->createNamedParameter($identifier, Connection::PARAM_STR)
                )
            )
            ->executeQuery()
            ->fetchOne();

        return $result;
    }

    public static function deleteSysFileProcessedfile(int $fileUid, ResourceStorage $storage): void
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable('sys_file_processedfile');
        $result = $queryBuilder
            ->select('*')
            ->from('sys_file_processedfile')
            ->where(
                $queryBuilder->expr()->eq(
                    'storage',
                    $queryBuilder->createNamedParameter($storage->getUid(), Connection::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'original',
                    $queryBuilder->createNamedParameter($fileUid, Connection::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();


        $processedBasePath = trim($storage->getProcessingFolder()->getStorage()->getConfiguration()['basePath'], '/');
        $environment = GeneralUtility::makeInstance(Environment::class);
        $publicPath = $environment->getPublicPath() . '/';
        foreach ($result as $processedFile) {
            $absoluteFilePath = $publicPath . $processedBasePath . $processedFile['identifier'];
            unlink($absoluteFilePath);
            $queryBuilder = DatabaseUtility::getQueryBuilderForTable('sys_file_processedfile');
            $queryBuilder
                ->delete('sys_file_processedfile')
                ->where(
                    $queryBuilder->expr()->eq(
                        'storage',
                        $queryBuilder->createNamedParameter(
                            $storage->getProcessingFolder()->getStorage()->getUid(),
                            Connection::PARAM_INT
                        )
                    ),
                    $queryBuilder->expr()->eq(
                        'identifier',
                        $queryBuilder->createNamedParameter(
                            $processedFile['identifier'],
                            Connection::PARAM_STR
                        )
                    )
                )
                ->executeStatement();
        }
    }
}
