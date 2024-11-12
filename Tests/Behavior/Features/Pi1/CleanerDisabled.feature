# Features/Pi1/DeletionPast.feature
@Pi1 @CleanerDisabled @Pi1DefaultDefault
Feature: Cleaner is disabled via TypoScipt and should display no error message

  Scenario: Check if no error message is displayed
    Given I am on "/plugin-not-configured-cleaner-disabled"
    Then I should not see "Powermail Cleaner configuration is not properly set. Please check plugin configuration."
