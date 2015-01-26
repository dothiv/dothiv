@UserReminder
Feature: Non-profit notification 6
  As the owner of a non-profit domain
  which is live
  I shall be reminded
  to tell the world

  Scenario: Send reminders
    Given the fixture "\Dothiv\CharityWebsiteBundle\Features\Fixture\UserReminderFixture" is loaded
    When "reminders" contains the result of calling "send" on the "dothiv.charity.userreminder.domain.telltheworld.nonprofit" service with values:
      | {\Dothiv\ValueObject\IdentValue@tell_the_world_nonprofit} |
    Then "{reminders}" should contain 2 element
    And "{reminders[0].ident}" should contain "non-profit-live-many-clicks.hiv"
    And "{reminders[1].ident}" should contain "non-profit-live-few-clicks.hiv"
