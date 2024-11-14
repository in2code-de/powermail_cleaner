# Features/Pi1/DeletionNoStorage.feature
@Pi1 @DeletionNotConfigured
Feature: Data won't be stored

  Scenario: Check if AllFields Form is rendered correctly
    Given I am on "plugin-not-configured"
    Then I should see "The form cannot be displayed until the necessary data protection information has been added. Please inform the editorial team about this form."
