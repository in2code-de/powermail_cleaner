<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Command;

use In2code\PowermailCleaner\Domain\Service\CleanupService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CleanupCommand extends Command
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \TYPO3\CMS\Core\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getQueryBuilderForTable('tt_content');

        $flexFormService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Service\FlexFormService::class);

        $cleanupService = GeneralUtility::makeInstance(\In2code\PowermailCleaner\Domain\Service\CleanupService::class);

        $powermailPlugins = $queryBuilder
            ->select('uid', 'pi_flexform')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('list')),
                $queryBuilder->expr()->eq('list_type', $queryBuilder->createNamedParameter('powermail_pi1'))
            )
            ->execute()
            ->fetchAll();

        foreach ($powermailPlugins as $powermailPlugin) {
            $flexForm = $flexFormService->convertFlexFormContentToArray($powermailPlugin['pi_flexform']);
            $formUid = $flexForm['settings']['flexform']['main']['form'];
            $cleanerConfiguration = $flexForm['settings']['flexform']['powermailCleaner'];
            $deletionBehavior = $cleanerConfiguration['deletionBehavior'];

            if (isset($deletionBehavior) && $deletionBehavior != '') {
                $cleanupService->cleanup($formUid, $powermailPlugin['uid'], $cleanerConfiguration);
            }
        }

        return 0;
    }
}