..  include:: /Includes.rst.txt
..  _configuration-typoscript:

TypoScript reference
====================

Please take care, that you include the typoScript Template for Powermail Cleaner and include it after the settings of Powermail.

This is the default configuration:

plugin.tx_powermail {
    view {
        templateRootPaths {
            10 = EXT:powermail_cleaner/Resources/Private/Templates/
        }

        partialRootPaths {
            10 = EXT:powermail_cleaner/Resources/Private/Partials/
        }

        layoutRootPaths {
            10 = EXT:powermail_cleaner/Resources/Private/Layouts/
        }
    }
    settings.setup.powermail_cleaner_enabled = 1
}


..  toctree::
    :maxdepth: 5
    :titlesonly:

    Setup
