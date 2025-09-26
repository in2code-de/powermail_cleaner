<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Command;

use In2code\Powermail\Domain\Repository\MailRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CleanupCommand extends Command
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Use this command to delete mails sent via EXT:powermail');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var \In2code\PowermailCleaner\Domain\Repository\MailRepository $mailRepository */
        $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
        $mailsToDelete = $mailRepository->findAllDeletionTimeStampOlderThan(time());
        $mails = 0;

        foreach ($mailsToDelete as $mail) {
            $mailRepository->removeFromDatabase($mail->getUid());
            $mails++;
        }
        $this->logger->info('Removed mails: ' . $mails);
        return Command::SUCCESS;
    }
}
