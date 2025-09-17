..  include:: /Includes.rst.txt
..  index:: Configuration
..  _configuration-general:

=====================
General configuration
=====================

Take care that you include the TypoScript Configuration Record in your installation

..  image:: /Images/Configuration.png
    :alt: Powermail Cleaner Icon
    :class: float-left with-shadow

Important: Include these settings after the settings of Powermail and ensure that you are not using the key "10" for
templateRootPaths, partialRootPaths and layoutRootPaths. Otherwise the Cleaner will not work correctly in the Frontend.


..  index::
    Configuration; Use Powermail Cleaner in a disabled state
    Configuration; Typical
..  _configuration_example:
..  _configuration_typical:


..  warning::
    Once you install the extension and include its configuration, the extension is active and will
    show warnings for forms which are not configured. Read the next section and learn how to prevent it.


..  _configuration_example:
Use Powermail Cleaner in a disabled state
=========================================

If you want to prepare your existing forms, you need to prevent, that all warnings in the Frontend are active. There are
two options to achieve this:

* Do not include the TypoScript Record "Powermail Cleaner Template"
* Disable the settings via TypoScript (see example below) - this is recommended if you want to activate the functionality partially.

..  code-block:: typoscript

    plugin.tx_powermail.settings.setup.powermail_cleaner_enabled = 0

..  _configuration_typical:

Typical configuration
=====================

Minimal example of TypoScript:

*   Powermail Cleaner adds its settings to the configuration of Powermail.
*   Below you can see the default configuration:

..  code-block:: typoscript

    plugin.tx_powermail {
        view {
            templateRootPaths {
                20 = EXT:powermail_cleaner/Resources/Private/Templates/
            }

            partialRootPaths {
                20 = EXT:powermail_cleaner/Resources/Private/Partials/
            }

            layoutRootPaths {
                20 = EXT:powermail_cleaner/Resources/Private/Layouts/
            }
        }
        settings.setup.powermail_cleaner_enabled = 1
    }
