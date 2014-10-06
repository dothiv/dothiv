Feature: Claim Domain without a token
  User user should be able to claim a domain without a token

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "user" with values:
      | handle    | userhandle          |
      | email     | someone@example.com |
      | firstname | John                |
      | surname   | Doe                 |
    Given the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user     | {user}                                       |
      | token    | usert0k3n                                    |
      | scope    | {\Dothiv\ValueObject\IdentValue@domainclaim} |
      | lifetime | {\DateTime@2014-01-02T13:44:15}              |
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

  Scenario: Claim domain without a token (when user email is owner email)
    Given the "DothivBusinessBundle:Domain" entity exists in "domain2" with values:
      | registrar  | {registrar}         |
      | name       | test2.hiv           |
      | token      | domaint0k3n2        |
      | ownerEmail | someone@example.com |
      | ownerName  | John Doe            |
    And I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a PUT request to "http://click4life.hiv.dev/api/domain/test2.hiv/claim"
    Then the response status code should be 201

  Scenario: Do not claim twice
    Given the "DothivBusinessBundle:Domain" entity exists in "domain2" with values:
      | registrar  | {registrar}         |
      | name       | test2.hiv           |
      | token      | domaint0k3n2        |
      | ownerEmail | someone@example.com |
      | ownerName  | John Doe            |
    And I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a PUT request to "http://click4life.hiv.dev/api/domain/test2.hiv/claim"
    Then the response status code should be 201
    And I send a PUT request to "http://click4life.hiv.dev/api/domain/test2.hiv/claim"
    Then the response status code should be 410

  Scenario: Claim domain without a token (when user email is not owner email)
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a PUT request to "http://click4life.hiv.dev/api/domain/test.hiv/claim"
    Then the response status code should be 202

  Scenario: Only send claim token once
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a PUT request to "http://click4life.hiv.dev/api/domain/test.hiv/claim"
    Then the response status code should be 202
    And I send a PUT request to "http://click4life.hiv.dev/api/domain/test.hiv/claim"
    Then the response status code should be 409
