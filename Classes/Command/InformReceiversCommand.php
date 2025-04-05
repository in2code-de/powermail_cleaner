<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Command;

use Doctrine\DBAL\Exception;
use In2code\PowermailCleaner\Domain\Model\Mail;
use In2code\PowermailCleaner\Domain\Repository\MailRepository;
use In2code\PowermailCleaner\Domain\Service\ReceiverAddressService;
use In2code\PowermailCleaner\Utility\DatabaseUtility;
use In2code\PowermailCleaner\Service\TimeCalculationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

class InformReceiversCommand extends Command
{
    private array $powermailCleanerTyposcript;
    private MailRepository $mailRepository;
    private FlexFormService $flexFormService;
    private Mail $mail;
    private TimeCalculationService $timeCalculationService;
    private array $receiversList = [];

    /**
     * @throws InvalidConfigurationTypeException
     */
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
        $this->powermailCleanerTyposcript = $this->getTypoScriptConfiguration();
        $this->mailRepository = GeneralUtility::makeInstance(MailRepository::class);
        $this->flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $this->mail = GeneralUtility::makeInstance(Mail::class);
        $this->timeCalculationService = GeneralUtility::makeInstance(TimeCalculationService::class);
    }

    protected function configure(): void
    {
        $this->setHelp('Informs receivers about the deletion of emails');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (empty($this->powermailCleanerTyposcript)) {
            $output->writeln('Powermail Cleaner: TypoScript configuration missing');
            $this->logger->critical('Powermail Cleaner: TypoScript configuration missing');
            return Command::FAILURE;
        }

        foreach ($this->getAllPowermailPi1PluginsWithDeletionRestriction() as $plugin) {
            $this->processPlugin($plugin);
        }

        foreach ($this->receiversList as $receiver) {
            $this->informReceiver($receiver);
        }

        return Command::SUCCESS;
    }

    private function getAllPowermailPi1PluginsWithDeletionRestriction(): array
    {
        /** @var DatabaseUtility $databaseUtility */
        $databaseUtility = GeneralUtility::makeInstance(DatabaseUtility::class);
        $powermailPi1Plugins = $databaseUtility->getPowermailPi1Plugins();

        $powermailPi1PluginsWithDeletionRestrictions = [];

        foreach ($powermailPi1Plugins as $plugin) {
            $flexform = $this->flexFormService->convertFlexFormContentToArray($plugin['pi_flexform']);

            if (ArrayUtility::isValidPath($flexform, 'settings/flexform/powermailCleaner')) {
                $cleanerSettings = $flexform['settings']['flexform']['powermailCleaner'];

                $informReceivers = $cleanerSettings['informReceiversBeforeDeletion'] ?? '';
                $plugin['period'] = (int)($cleanerSettings['informReceiversBeforeDeletionPeriod'] ?? 0);
                if ($informReceivers === '1' && $plugin['period'] > 0) {
                    $powermailPi1PluginsWithDeletionRestrictions[] = $plugin;
                }
            }
        }
        return $powermailPi1PluginsWithDeletionRestrictions;
    }

    /**
     * @throws InvalidConfigurationTypeException
     */
    private function getTypoScriptConfiguration(): array
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $typoscript = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        return
            !empty($typoscript['plugin.']['tx_powermail_cleaner.']['settings.'])
            ? $typoscript['plugin.']['tx_powermail_cleaner.']['settings.']
            : [];
    }

    private function findReceivers(array $flexform): array
    {
        /** @var  ReceiverAddressService $addressService */
        $addressService = GeneralUtility::makeInstance(ReceiverAddressService::class, $this->mail, $flexform);
        return $addressService->getReceiverEmails();
    }

    private function processPlugin(array $plugin): void
    {
        $flexform = $this->flexFormService->convertFlexFormContentToArray($plugin['pi_flexform']);
        $notificationTimeframe = $this->timeCalculationService->calculateNotificationTimeframe((int)$plugin['period']);

        $mailCount = $this->mailRepository->countMailsForPluginAndTranslatedWithinTimeframe($plugin['content_uid'], $notificationTimeframe);
        if ($mailCount > 0) {
            $receivers = $this->findReceivers($flexform['settings']['flexform']);
            if (count($receivers) > 0) {
                foreach ($receivers as $receiver) {
                    $receiverDetails = [
                      $receiver => [
                          'address' => $receiver,
                          'plugins' => [
                              $plugin['content_uid'] => [
                                  'plugin' => $plugin,
                                  'mailCount' => $mailCount,
                                  'notificationTimeframe' => $notificationTimeframe
                              ],
                          ],
                      ],
                    ];
                    ArrayUtility::mergeRecursiveWithOverrule($this->receiversList, $receiverDetails);
                }
            }
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function informReceiver(array $receiverInfo): void
    {
        $email = GeneralUtility::makeInstance(FluidEmail::class);
        $email
            ->to(new Address($receiverInfo['address']))
            ->from(
                new Address(
                    $this->powermailCleanerTyposcript['reminderMail.']['from.']['address'],
                    $this->powermailCleanerTyposcript['reminderMail.']['from.']['name']
                )
            )
            ->subject($this->powermailCleanerTyposcript['reminderMail.']['subject'])
            ->format(FluidEmail::FORMAT_BOTH)
            ->setTemplate($this->powermailCleanerTyposcript['reminderMail.']['template'])
            ->assign('plugins', $receiverInfo['plugins']);

        GeneralUtility::makeInstance(MailerInterface::class)->send($email);
    }
}
