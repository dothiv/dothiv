@Login @Error @LoginError
Feature: Login errors
  A user should be able to request a login link

  Background:
    Given I add "Content-Type" header equal to "application/json"

  Scenario: Request login link for non-existing user
    And I send a POST request to "http://click4life.hiv.dev/api/account/loginLink" with JSON values:
      | email  | SomeOne@Example.Com |
      | locale | en                  |
    Then the response status code should be 404

