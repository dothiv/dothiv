@UserReminder
Feature: User notification 4.1
  As the owner of a non-profit domain
  which I have registered
  I shall be reminded
  that the domain is not online

  @Current
  Scenario: Send reminders
    Given the fixture "\Dothiv\CharityWebsiteBundle\Features\Fixture\UserReminderFixture" is loaded
    When "reminders" contains the result of calling "send" on the "dothiv.charity.userreminder.nonprofit_registered_but_not_online" service with values:
      | {\Dothiv\ValueObject\IdentValue@registered_but_not_online} |
    Then "{reminders}" should contain 1 element
    And "{reminders[0].ident}" should contain "non-profit-registered-not-online.hiv"
