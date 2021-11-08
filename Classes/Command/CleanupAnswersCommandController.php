<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\Command;

use In2code\PowermailCleaner\Domain\Service\CleanupService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

class CleanupAnswersCommandController extends CommandController
{
    /**
     * @var CleanupService
     */
    protected $cleanupService;

    /** @var array */
    public $settings;

    /**
     * @param CleanupService $cleanupService
     */
    public function injectCleanupService(CleanupService $cleanupService): void
    {
        $this->cleanupService = $cleanupService;
    }
    /**
     * @param int $age Age of the answers in seconds. e.g. 5184000 = 60 days
     * @param string $pid Optional PID. If set, only answers stored on the given PID are cleaned up.
     * @return bool
     */
    public function deleteCommand(int $age, string $pid = null): bool
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);

        $pids = GeneralUtility::trimExplode(',', $pid, true);
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $this->cleanupService->settings = $this->settings['module.']['tx_powermail.']['settings.'];
        foreach ($pids as $pidToClean) {
            $stats = $this->cleanupService->deleteMailsOlderThanAgeInPid($age, (int) $pidToClean);
            $this->outputLine(
                sprintf(
                    'Deleted %d mails containing %d answers and %d files',
                    $stats['mails'],
                    $stats['answers'],
                    $stats['files']
                )
            );
        }

        return true;
    }
}
