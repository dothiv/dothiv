@UserReminder
Feature: User notification 5.1
  As the owner of a .hiv domain
  I shall be reminded
  that the domain needs to be configured

  Scenario: Send reminders
    Given the fixture "\Dothiv\CharityWebsiteBundle\Features\Fixture\UserReminderFixture" is loaded
    When "reminders" contains the result of calling "send" on the "dothiv.charity.userreminder.domain.online_but_click_counter_not_configured" service with values:
      | {\Dothiv\ValueObject\IdentValue@domain_online_but_click_counter_not_configured} |
    Then "{reminders}" should contain 1 element
    And "{reminders[0].ident}" should contain "online-but-not-configured.hiv"
