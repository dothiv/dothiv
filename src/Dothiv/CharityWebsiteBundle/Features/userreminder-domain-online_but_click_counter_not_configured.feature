@UserReminder
Feature: User notification 5.1
  As the owner of a .hiv domain
  I shall be reminded
  that the domain needs to be configured

  Scenario: Send reminders (non-profit)
    Given the fixture "\Dothiv\CharityWebsiteBundle\Features\Fixture\UserReminderFixture" is loaded
    When "reminders" contains the result of calling "send" on the "dothiv.charity.userreminder.domain.online_but_click_counter_not_configured.nonprofit" service with values:
      | {\Dothiv\ValueObject\IdentValue@domain_online_but_click_counter_not_configured_nonprofit} |
    Then "{reminders}" should contain 1 element
    And "{reminders[0].ident}" should contain "non-profit-online-but-not-configured.hiv"

  Scenario: Send reminders (for-profit)
    Given the fixture "\Dothiv\CharityWebsiteBundle\Features\Fixture\UserReminderFixture" is loaded
    When "reminders" contains the result of calling "send" on the "dothiv.charity.userreminder.domain.online_but_click_counter_not_configured.forprofit" service with values:
      | {\Dothiv\ValueObject\IdentValue@domain_online_but_click_counter_not_configured_forprofit} |
    Then "{reminders}" should contain 1 element
    And "{reminders[0].ident}" should contain "for-profit-online-but-not-configured.hiv"
