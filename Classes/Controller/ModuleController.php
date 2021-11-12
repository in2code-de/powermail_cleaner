<?php
declare(strict_types=1);
namespace In2code\PowermailCleaner\Controller;

use In2code\PowermailCleaner\Domain\Service\CleanupService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Class ModuleController for backend modules
 */
class ModuleController extends \In2code\Powermail\Controller\ModuleController
{

    /**
     * Check Permissions
     *
     * @return void
     */
    public function initializeCleanupAction()
    {
        #deactive this, to restrict this only for admins
        #$this->checkAdminPermissions();
    }

    /**
     * @param int $age
     * @param int $pid
     */
    public function cleanupAction(int $age = null, int $pid = null): void
    {
        if ($age === null) {
            if ($this->settings['cleanupAge']) {
                $age = $this->settings['cleanupAge'];
            }

            // Retrieve page TSconfig of currently logged in user
            $pageTsConfig = BackendUtility::getPagesTSconfig(GeneralUtility::_GET('id'));
            if ($pageTsConfig['tx_powermail.']['settings.']['cleanupAge']) {
                $age = (int)$pageTsConfig['tx_powermail.']['settings.']['cleanupAge'] ;
            }

            // Retrieve user TSconfig of currently logged in user
            $userTsConfig = $GLOBALS['BE_USER']->getTSConfig();
            // check if the age is set in UserTS
            if ($userTsConfig['tx_powermail.']['settings.']['cleanupAge']) {
                $age = (int)$userTsConfig['tx_powermail.']['settings.']['cleanupAge'] ;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && null !== $age && $age > 0) {
            $cleanupService = $this->objectManager->get(CleanupService::class);
            $stats = $cleanupService->deleteMailsOlderThanAgeInPid($age, $pid);
            $this->view->assign('stats', $stats);
        }
        $this->view->assign('pid', $pid ?: GeneralUtility::_GET('id') ?: null);
        $this->view->assign('age', $age);
    }
}
