<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\EventListener;

use In2code\Powermail\Events\CheckIfMailIsAllowedToSaveEvent;
use In2code\PowermailCleaner\Domain\Model\Mail;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;

final class CheckIfMailIsAllowedToSaveEventListener
{
    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly FlexFormService $flexFormService,)
    {}

    public function __invoke(CheckIfMailIsAllowedToSaveEvent $event): void
    {
        /**
         * @var Mail $mail
         */
        $mail = $event->getMail();
        $plugin = $mail->getPlugin();

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $queryBuilder
            ->select('uid', 'pi_flexform')
            ->from('tt_content')
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($plugin)))
            ->setMaxResults(1);

        $result = $queryBuilder->executeQuery()->fetchAssociative();
        $flexFormArray = $this->flexFormService->convertFlexFormContentToArray($result['pi_flexform']);
        if (!empty($flexFormArray['settings']['flexform']['powermailCleaner']['deletionBehavior']) && $flexFormArray['settings']['flexform']['powermailCleaner']['deletionBehavior'] === 'dbDisable') {
            $event->setSavingOfMailAllowed(false);
        }
    }
}
