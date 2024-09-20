..  include:: /Includes.rst.txt
..  highlight:: php

..  _developer:

================
Developer corner
================

Use this section to provide examples of code or detail any information that would be deemed relevant to a developer.

For example explain how a certain feature was implemented.


..  _developer-api:

Signals
=======

Powermail Cleaner uses Signal to perform its opeartions:

Powermail: createActionAfterMailDbSaved => This is adding the $pluginUid of the powermail plugin to the mail - then it is possible to build a relation from the mail to the used plugin

Powermail: checkIfMailIsAllowedToSave ==> Checks if the powermail cleaner settings are configured, otherwise the mails can not be stored.

Powermail: createActionBeforeRenderView ==> adds a timestamp for a mail, that determines, when the mail can be deleted.

t3lib/class.t3lib_tcemain.php']['processDatamapClass => SynchronizeLocalizedRecordsHook => this synchronizes the flexform settings from the default language to translations (if free mode is not used)