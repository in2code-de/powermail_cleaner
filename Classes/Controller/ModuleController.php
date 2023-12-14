<?php
declare(strict_types=1);
namespace In2code\PowermailCleaner\Controller;

use In2code\PowermailCleaner\Domain\Service\CleanupService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Class ModuleController for backend modules
 */
class ModuleController extends \In2code\Powermail\Controller\ModuleController
{
    /**
     * @param int $age
     * @param int $pid
     */
    public function cleanupAction(): ResponseInterface
    {
        $age = null;
        $pid = null;
        
        if ($this->request->hasArgument('age')) {
            $age = $this->request->getArgument('age');
        }
        if ($this->request->hasArgument('pid')) {
            $pid = $this->request->getArgument('pid');
        }
        $this->id = (int)$this->request->getArgument('id');

        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setTitle('Powermail');
        $this->moduleTemplate->setFlashMessageQueue($this->getFlashMessageQueue());
        $this->moduleTemplate->makeDocHeaderModuleMenu(['id' => $this->id]);

        $this->moduleTemplate->assign('pid', $pid ?: $this->id ?: null);
        $this->moduleTemplate->assign('age', $age);

        // Why this "magic"? It's totally intransparent for the admin user, because he
        // never sees those values.
        // And cleanup age does not make really sense, because it may contradict the settings
        // in all plugins

        if ($age === null) {
            if ($this->settings['cleanupAge']) {
                $age = $this->settings['cleanupAge'];
            }

            // Retrieve page TSconfig of currently logged in user
            $pageTsConfig = BackendUtility::getPagesTSconfig($this->id);
            if ($pageTsConfig['tx_powermail.']['settings.']['cleanupAge']) {
                $age = (int)$pageTsConfig['tx_powermail.']['settings.']['cleanupAge'];
            }

            // Retrieve user TSconfig of currently logged in user
            $userTsConfig = $GLOBALS['BE_USER']->getTSConfig();
            // check if the age is set in UserTS
            if ($userTsConfig['tx_powermail.']['settings.']['cleanupAge']) {
                $age = (int)$userTsConfig['tx_powermail.']['settings.']['cleanupAge'];
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && null !== $age && $age > 0) {
            // $cleanupService = GeneralUtility::makeInstance(CleanupService::class);
            // ToDo: This won't work at all b/c the method does not exist (in any version)
            //$stats = $cleanupService->deleteMailsOlderThanAgeInPid($age, $pid);
            //$this->moduleTemplate->assign('stats', $stats);
        }

        return $this->moduleTemplate->renderResponse('Module/Cleanup');
    }
}
