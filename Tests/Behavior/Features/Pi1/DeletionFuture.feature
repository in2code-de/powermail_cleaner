# Features/Pi1/DeletionFuture.feature
@Pi1 @DeletionDate @Pi1DefaultDefault
Feature: Data will be cleaned after the specified deletion date in the future
  Check output of settings

  Scenario: Check if AllFields Form is rendered correctly
    Given I am on "data-will-be-cleaned-after-the-specified-deletion-date"
    Then I should see "The data you submitted to this form will be deleted on 28.07.2030."
    Then I should see "Contact for objection and deletion option"
    Then I should see "Amendments to the Data Protection Policy"

# todo: Enter mails
# todo: check delection timestamp
# todo: run cleaner
# todo: check if mail still exists
