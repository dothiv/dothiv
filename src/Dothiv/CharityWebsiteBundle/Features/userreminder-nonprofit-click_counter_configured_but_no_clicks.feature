@UserReminder
Feature: User notification 7.
  As the owner of a non-profit domain
  I shall be reminded
  that I have not received clicks
  even if I the click-counter is configured and the domain is live

  Scenario: Send reminders
    Given the "DothivBusinessBundle:User" entity exists in "user" with values:
      | handle    | userhandle          |
      | email     | someone@example.com |
      | firstname | John                |
      | surname   | Doe                 |
    And the "DothivBusinessBundle:Registrar" entity exists in "registrar" with values:
      | extId | 1234-AC        |
      | name  | ACME Registrar |
    And the "DothivBusinessBundle:Domain" entity exists in "domain" with values:
      | registrar  | {registrar}          |
      | name       | test.hiv             |
      | owner      | {user}               |
      | nonprofit  | true                 |
      | live       | true                 |
      | clickcount | 1                    |
      | created    | {\DateTime@-4 weeks} |
    When "reminders" contains the result of calling "send" on the "dothiv.charity.userreminder.nonprofit.click_counter_configured_but_no_clicks" service with values:
      | {\Dothiv\ValueObject\IdentValue@nonprofit_click_counter_configured_but_no_clicks} |
    Then I debug "{reminders}"
