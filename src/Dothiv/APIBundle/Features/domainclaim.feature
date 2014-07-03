Feature: Domain Claim
  User user should be able to claim a domain

  Scenario: Claim domain
    Given the "DothivBusinessBundle:User" entity exists in "user" with values:
      | email   | someone@example.com |
      | token   | usert0k3n           |
      | surname | John                |
      | name    | Doe                 |
    And the "DothivBusinessBundle:Domain" entity exists in "domain" with values:
      | name  | test.hiv    |
      | token | domaint0k3n |
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a POST request to "http://click4life.hiv.dev/api/domain/claim" with values:
      | domain | test.hiv    |
      | token  | domaint0k3n |
    Then the response status code should be 201

    # Verify claimed domain
    Given I add "Accept" header equal to "application/json"
    And I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a GET request to "http://click4life.hiv.dev/api/user/domains"
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON object should be a list with 1 element
