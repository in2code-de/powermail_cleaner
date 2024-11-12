..  include:: /Includes.rst.txt

..  _installation:

============
Installation
============

(1) Install the extension via Extension Manager (Classic Mode) or require it via composer._installation

..  code-block:: bash

    composer req in2code/powermail_cleaner

Dependencies
------------

Powermail Cleaner requires to have Powermail installed.

(2) Setup a Scheduler Task "Execute console commands (scheduler)" => "powermail_cleaner:cleanup"

Please take care, that your Scheduler (:ref:`t3scheduler:introduction`) is properly setup.
