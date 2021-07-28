.. include:: ../Includes.txt
.. include:: Images.txt

.. _introduction:

Introduction
============

.. only:: html

	:ref:`what`  | :ref:`screenshots` |

.. _what:

What does it do?
----------------

Powermail Cleaner offers a new command (scheduler task) to delete old form answers including related uploads.
Further you get a new option in the backend view to cleanup all answers (or only the selected page) restricted to a
given time period in seconds.

.. _screenshots:

Example Screenshots
-------------------

Backend View
^^^^^^^^^^^^

|backend1|

Example Backend Cleaner view


Configuration
-------------

The clean up age can be set via three ways:

typoScript (Module Settings):
module.tx_powermailcleanupAge = 31536000

UserTS / PageTS
tx_powermail.settings.cleanupAge = XXX

Order of processing:
* typoScript
* PageTS
* UserTS
