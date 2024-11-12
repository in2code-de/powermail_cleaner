..  include:: /Includes.rst.txt
..  highlight:: typoscript
..  index::
    TypoScript; Setup
..  _configuration-typoscript-setup:

Setup
=====

Enable / disable some options
-----------------------------

..  confval:: powermail_cleaner_enabled

    :type: bool
    :Default: 1

    If :typoscript:`1` then the Cleaner Function is enabled in the Frontend.


    Example::

       plugin.tx_powermail {
            settings.setup.powermail_cleaner_enabled = 1
        }

..  confval:: templateRootPaths

    :type: String
    :Default: 10 = EXT:powermail_cleaner/Resources/Private/Templates/

    Adds the templates for Powermail Cleaner.


    Example::

       plugin.tx_powermail {
           view {
               templateRootPaths {
                   10 = EXT:powermail_cleaner/Resources/Private/Templates/
               }
           }
       }
..  confval:: partialRootPaths

    :type: String
    :Default: 10 = EXT:powermail_cleaner/Resources/Private/Partials/

    Adds the Partials for Powermail Cleaner.

    Example::

       plugin.tx_powermail {
           view {
               partialRootPaths {
                           10 = EXT:powermail_cleaner/Resources/Private/Partials/
                }
           }
       }

..  confval:: layoutRootPaths

    :type: String
    :Default: 10 = EXT:powermail_cleaner/Resources/Private/Layouts/

    Adds the Layouts for Powermail Cleaner.

    Example::

       plugin.tx_powermail {
           view {
               layoutRootPaths {
                           10 = EXT:powermail_cleaner/Resources/Private/Layouts/
                }
           }
       }
