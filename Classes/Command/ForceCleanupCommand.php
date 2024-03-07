<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Command;

use In2code\Powermail\Domain\Repository\MailRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ForceCleanupCommand extends Command
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Use this command to force delete mails sent via EXT:powermail after a certain amount of time')
            ->addArgument(
                'Retention days',
                InputArgument::REQUIRED,
                'After how many days should the emails be deleted?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
        // ToDo: calculate timestamp
        $timestamp = (time() - $input->getArgument('Retention days') * 86400);
        $mailsToDelete = $mailRepository->findMailsOlderThan($timestamp);
        $output->writeln( 'Mails found: ' . count($mailsToDelete));
        $mails = 0;

        foreach ($mailsToDelete as $mail) {
            $mailRepository->removeMail($mail['uid']);
            $mails++;
        }
        $this->logger->info('Removed mails: ' . $mails);
        return Command::SUCCESS;
    }
}
