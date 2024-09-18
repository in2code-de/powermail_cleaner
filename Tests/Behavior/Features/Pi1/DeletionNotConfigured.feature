# Features/Pi1/DeletionNoStorage.feature
@Pi1 @DeletionNotConfigured
Feature: Data won't be stored

  Scenario: Check if AllFields Form is rendered correctly
    Given I am on "plugin-not-configured"
    Then I should see "Powermail Cleaner configuration is not properly set. Please check plugin configuration."
