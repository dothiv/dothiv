@UserReminder
Feature: User notification 2
  As the applicant for a non-profit domain
  which has been approved
  I shall be reminded
  that the domain is net yet registered

  Scenario: Send reminders
    Given the fixture "\Dothiv\CharityWebsiteBundle\Features\Fixture\UserReminderFixture" is loaded
    When "reminders" contains the result of calling "send" on the "dothiv.charity.userreminder.nonprofit_approved_not_registered" service with values:
      | {\Dothiv\ValueObject\IdentValue@nonprofit_approved_not_registered} |
    Then "{reminders}" should contain 1 element
    And "{reminders[0].ident}" should contain "non-profit-registered-not-registered.hiv"
