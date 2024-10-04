..  include:: /Includes.rst.txt

..  _for-editors:

===========
For Editors
===========

How to use the extension powermail cleaner.

The functionality is located in the Plugin Settings of a Powermail Form Plugin at the register "Data protection / Clean-up"

..  figure:: /Images/Plugin1.png
    :class: with-shadow
    :alt: Backend view

    Default Plugin view

Please choose one of the options in the select box "Clean-up of form data". Depending of your choice options will be shown.Backend

..  warning::
    If you do not select one option, a warning will be shown in the Frontend. If you want to prepare your forms and then active the cleaner functionality, please refer to _configuration_example:


Option 1: Do not store in database
----------------------------------

..  figure:: /Images/Plugin1.png
    :class: with-shadow
    :alt: Screenshot Flexform Option 1 - Do not store in database

    Screenshot Option 1: "Do not store in database"

If this option is selected, no form answers are stored in the database. This is usefull, if you want to use "just"
receiving e-mails from the form or if you use customized finishers. Please be aware, that answers can not be restored,
as they are never saved to the database.


Option 2: Data will be cleaned after specified date
---------------------------------------------------

..  figure:: /Images/Plugin2a.png
    :class: with-shadow
    :alt: Screenshot Flexform Option 2 - Data will be cleaned after specified data

    Screenshot Option 2: "Data will be cleaned after specified data"

This option allows you to define a specific data after all answers of this form will get deleted. Note: Automatic delection
is only possible, if the necessary scheduler task is set up (see configuration).
Further you have two options to add information about the "Contact for objection and deletion option" and
"Amendments to the Data Protection Policy"

..  figure:: /Images/Plugin2.png
    :class: with-shadow
    :alt: Screenshot Flexform Option 2 - ammendments

    Screenshot Option 2: ammendments

Option 3: Data will be cleaned after given days
-----------------------------------------------

..  figure:: /Images/Plugin3.png
    :class: with-shadow
    :alt: Screenshot Flexform Option 3 - Data will be cleaned after given days

    Screenshot Option 3: "Data will be cleaned after given days"

This option allows you to define a clean up period. Data will be cleaned after the given period. Note: Automatic delection
is only possible, if the necessary scheduler task is set up (see configuration).
Further you have two options to add information about the "Contact for objection and deletion option" and
"Amendments to the Data Protection Policy" (Same as in Option 2)
