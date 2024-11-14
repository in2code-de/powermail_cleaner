# Features/Pi1/DeletionFixed.feature
@Pi1 @DeletionDate @Pi1DefaultDefault
Feature: Data will be cleaned after the specified deletion date in the past
  Check output of settings

  Scenario: Check if Form is rendered correctly
    Given I am on "data-will-be-cleaned-after-the-specified-deletion-period"
    Then I should see "The data you submitted to this form will be deleted after a period of 14 days."
    Then I should see "Contact for objection and deletion option"
    Then I should see "Amendments to the Data Protection Policy"

#  Scenario: Check if Form is rendered correctly in German
#    Given I am on "de/de-data-will-be-cleaned-after-the-specified-deletion-period"
#    Then I should see "Ihre Eingaben in diesem Formular werden nach 14 Tagen gelöscht."
#    Then I should see "Kontakt für Einspruch und Löschungsmöglichkeit"
#    Then I should see "Änderungen an der Datenschutzpolitik"

# todo: create mails
# todo: update with old timestamp
# todo: run cleaner
# todo: check if mail is deleted
