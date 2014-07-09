Feature: Login
  A user should be request a login link

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "user" with values:
      | handle  | userhandle          |
      | email   | someone@example.com |
      | surname | John                |
      | name    | Doe                 |
    And I add "Accept" header equal to "application/json"
    And I add "Content-Type" header equal to "application/json"

  Scenario: Request login link
    Given I send a POST request to "http://click4life.hiv.dev/api/account/loginLink" with JSON values:
      | email | someone@example.com |
    Then the response status code should be 201
    # Second login link should not be created
    Given I send a POST request to "http://click4life.hiv.dev/api/account/loginLink" with JSON values:
      | email | someone@example.com |
    Then the response status code should be 429
    And the header "Retry-After" should be equal to "1800"
