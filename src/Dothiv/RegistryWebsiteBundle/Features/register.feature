Feature: Register Account
  A user should be able register his account

  Scenario: Fetch User Info
    Given I add "Accept" header equal to "application/json"
    And I send a POST request to "http://tld.hiv.dev/api/account" with JSON values:
      | email   | someone@example.com |
      | surname | John                |
      | name    | Doe                 |
    Then the response status code should be 201
    And the header "content-type" should contain "application/json"
    And the JSON node "email" should contain "someone@example.com"
    And the JSON node "surname" should contain "John"
    And the JSON node "name" should contain "Doe"
