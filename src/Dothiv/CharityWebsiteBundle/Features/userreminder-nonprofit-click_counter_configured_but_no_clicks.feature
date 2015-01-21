@UserReminder @Skip
Feature: User notification 7.
  As the owner of a non-profit domain
  I shall be reminded
  that I have not received clicks
  even if I the click-counter is configured and the domain is live

  Scenario: Send reminders
    Given the fixture "\Dothiv\CharityWebsiteBundle\Features\Fixture\UserReminderFixture" is loaded
    When "reminders" contains the result of calling "send" on the "dothiv.charity.userreminder.nonprofit.click_counter_configured_but_no_clicks" service with values:
      | {\Dothiv\ValueObject\IdentValue@nonprofit_click_counter_configured_but_no_clicks} |
    Then "{reminders}" should contain 1 element
    And "{reminders[0].ident}" should contain "non-profit-live-few-clicks.hiv"
