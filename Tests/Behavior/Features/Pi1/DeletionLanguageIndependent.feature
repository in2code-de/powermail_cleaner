# Features/Pi1/DeletionLanguageIndependent.feature
@Pi1 @DeletionNoStorage
Feature: Data won't be stored

  Scenario: Check if AllFields Form is rendered correctly
    Given I am on "language-independent"
    Then I should see "The data you submitted to this form will not be stored in the database."
    Then I should see "Amendments to the Data Protection Policy"

  Scenario: Check if Form is rendered correctly in German and the setup is different
    Given I am on "de/de-language-independent"
    Then I should see "19.05.2027"
    Then I should see "Kontakt für Einspruch und Löschmöglichkeit"
    Then I should see "Änderungen an der Datenschutzpolitik"

# todo: Enter mails
# todo: check if no mails are stared
