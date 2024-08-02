# Features/TYPO3/Start.feature
@TYPO3 @TYPO3Start
Feature: Search
  Basic test to see if TYPO3 instance is working

  Scenario: Searching for the homepage that does exist
    Given I am on "/"
    Then I should see "Welcome at Powermail Cleaner Testparcour"
    Given I am on "/de/"
    Then I should see "Willkommen im Powermail Cleaner Testparcour"
