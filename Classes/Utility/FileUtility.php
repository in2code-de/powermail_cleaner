<?php

namespace In2code\PowermailCleaner\Utility;

use DASPRiD\Enum\Exception\CloneNotSupportedException;
use TYPO3\CMS\Core\Configuration\SiteConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileUtility
{
    public static function getFilesInformation(
        string $answerValue,
        string $uploadFolder,
        ?ResourceStorage $storage = null
    ): array
    {
        $fileInformation = [];

        // strip leading and closing characters from
        $answerValue = str_replace(['["', '"]'], '', $answerValue);

        // split answer value into single filenames
        $files = GeneralUtility::trimExplode('","', $answerValue, true);
        foreach ($files as $file) {
            $identifier = self::buildIdentifier($file, $uploadFolder, $storage);
            $sysFileUid = false;
            if ($storage instanceof ResourceStorage) {
                $sysFileUid = self::getSysFileUid($storage->getUid(), $identifier);
            }
            $fileInformation[] = [
                'uid' => $sysFileUid,
                'identifier' => $identifier,
            ];
        }
        return $fileInformation;
    }

    private static function buildIdentifier(string $filename, string $uploadFolder, ?ResourceStorage $storage = null)
    {
        $uploadFolderWithoutResourcePath = trim($uploadFolder, '/');
        if ($storage !== null) {
            $uploadFolderWithoutResourcePath = self::stripResourcePath($storage, $uploadFolder);
        }
        $identifier = $uploadFolderWithoutResourcePath . '/' . $filename;
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

    public static function deleteFromFilesystem(string $identifier, ?ResourceStorage $storage): void
    {
        $resourceBasePath = '';
        if ($storage instanceof ResourceStorage) {
            $resourceBasePath = $storage->getConfiguration()['basePath'];
        }

        $publicPath = (GeneralUtility::makeInstance(Environment::class))->getPublicPath() . '/';
        $absoluteFilePath = str_replace(
            '//',
            '/',
            $publicPath . $resourceBasePath . $identifier
        );

        @unlink($absoluteFilePath);
    }

    public static function deleteSysfile(array $file, ResourceStorage $storage): void
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable('sys_file');
        $queryBuilder
            ->delete('sys_file')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter(
                        $file['uid'],
                        Connection::PARAM_INT
                    )
                )
            )
            ->executeStatement();
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

        $processedBasePath = rtrim($storage->getProcessingFolder()->getStorage()->getConfiguration()['basePath'], '/');
        $environment = GeneralUtility::makeInstance(Environment::class);
        $publicPath = $environment->getPublicPath() . '/';
        foreach ($result as $processedFile) {
            $absoluteFilePath = $publicPath . $processedBasePath . $processedFile['identifier'];
            @unlink($absoluteFilePath);
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

    public static function deleteSysFileReference(int $fileUid): void
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable('sys_file_reference');
        $queryBuilder
            ->delete('sys_file_reference')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid_local',
                    $queryBuilder->createNamedParameter($fileUid,Connection::PARAM_INT)
                )
            )
            ->executeStatement();
    }
}
