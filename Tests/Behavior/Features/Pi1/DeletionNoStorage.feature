# Features/Pi1/DeletionNoStorage.feature
@Pi1 @DeletionNoStorage
Feature: Data won't be stored

  Scenario: Check if AllFields Form is rendered correctly
    Given I am on "no-data-stored"
    Then I should see "The data you submitted to this form will not be stored in the database."
    Then I should see "Amendments to the Data Protection Policy"


# todo: Enter mails
# todo: check if no mails are stared
