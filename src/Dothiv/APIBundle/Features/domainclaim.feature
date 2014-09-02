Feature: Claim Domain
  User user should be able to claim a domain

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "user" with values:
      | handle    | userhandle          |
      | email     | someone@example.com |
      | firstname | John                |
      | surname   | Doe                 |
    Given the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user     | {user}                                                      |
      | token    | usert0k3n                                                   |
      | scope    | {\Dothiv\BusinessBundle\ValueObject\IdentValue@domainclaim} |
      | lifetime | {\DateTime@2014-01-02T13:44:15}                             |
    And the "DothivBusinessBundle:Registrar" entity exists in "registrar" with values:
      | extId | 1234-AC        |
      | name  | ACME Registrar |
    And the "DothivBusinessBundle:Domain" entity exists in "domain" with values:
      | registrar  | {registrar}  |
      | name       | test.hiv     |
      | token      | domaint0k3n  |
      | ownerEmail | john@doe.com |
      | ownerName  | John Doe     |
    And I add "Accept" header equal to "application/json"

  Scenario: Claim domain
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a POST request to "http://click4life.hiv.dev/api/domain/claim" with JSON values:
      | token | domaint0k3n |
    Then the response status code should be 201
    And the JSON node "name" should contain "test.hiv"

    # Verify claimed domain
    And I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a GET request to "http://click4life.hiv.dev/api/user/userhandle/domains"
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON object should be a list with 1 element
    And "name" on the JSON list 0 should be "test.hiv"

  Scenario: Failed claim for invalid username
    Given I add Bearer token equal to "wrongt0k3n"
    And I send a POST request to "http://click4life.hiv.dev/api/domain/claim" with JSON values:
      | token | domaint0k3n |
    Then the response status code should be 403

  Scenario: Failed claim for wrong token
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a POST request to "http://click4life.hiv.dev/api/domain/claim" with JSON values:
      | token | invalidt0k3n |
    Then the response status code should be 400

  Scenario: Claim domain from other user
    Given the "DothivBusinessBundle:User" entity exists in "jane" with values:
      | handle    | jane             |
      | email     | jane@example.com |
      | firstname | Jane             |
      | surname   | Doe              |
    Given the "DothivBusinessBundle:UserToken" entity exists in "janeToken" with values:
      | user     | {jane}                                                      |
      | token    | j4n3st0k3n                                                  |
      | scope    | {\Dothiv\BusinessBundle\ValueObject\IdentValue@domainclaim} |
      | lifetime | {\DateTime@2014-01-02T13:44:15}                             |
    Given I add Bearer token equal to "abc8c04a75255c72ba3952421272caf4294acf06"
    And I send a POST request to "http://click4life.hiv.dev/api/domain/claim" with JSON values:
      | token | domaint0k3n |
    Then the response status code should be 201
    And the JSON node "name" should contain "test.hiv"
    # Verify claimed domain
    And I send a GET request to "http://click4life.hiv.dev/api/user/jane/domains"
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON object should be a list with 1 element
    And "name" on the JSON list 0 should be "test.hiv"
