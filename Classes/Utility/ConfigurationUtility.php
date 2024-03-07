<?php

namespace In2code\PowermailCleaner\Utility;

use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class ConfigurationUtility
{
    public static function getFullTypoScriptConfiguration(): array
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        return $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
    }

    public static function getPowermailUploadFolder(): string
    {
        $typoScriptConfiguration = self::getFullTypoScriptConfiguration();
        return $typoScriptConfiguration['plugin.']['tx_powermail.']['settings.']['setup.']['misc.']['file.']['folder'];
    }

    /**
     * @return array
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function getExtensionConfiguration(): array
    {
        return (array)GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('powermail_cleaner');
    }
}
