<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Hooks;

use In2code\Powermail\Controller\FormController;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CheckIfMailIsAllowedToSave
{

    private ConnectionPool $connectionPool;

    private FlexFormService $flexFormService;

    public function injectConnectionPool(ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;
    }

    public function injectFlexFormService(FlexFormService $flexFormService)
    {
        $this->flexFormService = $flexFormService;
    }

    /**
     * @param $isSavingOfMailAllowed boolean
     * @param $mail \In2code\PowermailCleaner\Domain\Model\Mail
     * @param $formController \In2code\PowermailCleaner\Controller\FormController
     * @return void
     */
    public function check(&$isSavingOfMailAllowed, $mail, $formController)
    {
        $formController->getContentObject()->data['uid'];
        /** @var \In2code\PowermailCleaner\Controller\FormController $formController */
        $pluginUid = $formController->getContentObject()->data['uid'];

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $queryBuilder
            ->select('uid', 'pi_flexform')
            ->from('tt_content')
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($pluginUid)))
            ->setMaxResults(1);

        $result = $queryBuilder->executeQuery()->fetchAssociative();
        $flexFormArray = $this->flexFormService->convertFlexFormContentToArray($result['pi_flexform']);
        if (!empty($flexFormArray['settings']['flexform']['powermailCleaner']['deletionBehavior']) && $flexFormArray['settings']['flexform']['powermailCleaner']['deletionBehavior'] === 'dbDisable') {
            $isSavingOfMailAllowed = false;
        }
    }
}
