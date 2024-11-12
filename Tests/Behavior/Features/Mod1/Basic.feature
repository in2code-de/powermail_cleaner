# Features/Mod1/Basic.feature
@Mod1 @Mod1Basic
Feature: Basic
  Basically checks if the backend module exists

  @javascript
  Scenario: Login into backend
    Given I am on "/typo3/index.php"
    Then the sourcecode should contain 'typo3-login-logo'
    When I fill in "username" with "admin"
    When I fill in "p_field" with "password"
    And I press "t3-login-submit"

  # Disable
    #Given I am on "typo3/module/web/PowermailM1?id=15"
    # Then the response should contain "cleanup"
