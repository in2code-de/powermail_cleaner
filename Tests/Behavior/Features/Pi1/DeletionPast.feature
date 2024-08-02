# Features/Pi1/DeletionPast.feature
@Pi1 @DeletionDate @Pi1DefaultDefault
Feature: Data will be cleaned after the specified deletion date in the past
  Check output of settings

  Scenario: Check if AllFields Form is rendered correctly
    Given I am on "data-will-be-cleaned-after-the-specified-deletion-date-in-the-past"
    Then I should see "The data you submitted to this form will be deleted on 28.07.2023."
    Then I should see "This form is disabled, as the deletion period is reached. Please inform the editor of this form."
    Then I should see "Amendments to the Data Protection Policy"

# todo: Enter mails
# todo: check delection timestamp
# todo: run cleaner
# todo: check if mail still exists
