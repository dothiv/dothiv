@UserReminder
Feature: Non-profit notification 3
  As the owner of a for-profit domain
  which is online
  and where I have configured the click-counter
  but did not install it
  I shall be reminded to do so

  Scenario: Send reminders
    Given the fixture "\Dothiv\CharityWebsiteBundle\Features\Fixture\UserReminderFixture" is loaded
    When "reminders" contains the result of calling "send" on the "dothiv.charity.userreminder.forprofit.online_clickcounter_configured_but_not_installed" service with values:
      | {\Dothiv\ValueObject\IdentValue@forprofit_online_clickcounter_configured_but_not_installed} |
    Then "{reminders}" should contain 1 element
    And "{reminders[0].ident}" should contain "for-profit-online-configured-click-counter-but-not-installed.hiv"
