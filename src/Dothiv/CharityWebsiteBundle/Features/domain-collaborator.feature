@Domain @DomainCollaborator
Feature: Share domain
  A user
  I should be able to allow another user to manage my domain

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "user" with values:
      | handle    | userhandle          |
      | email     | someone@example.com |
      | firstname | John                |
      | surname   | Doe                 |
    And the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user     | {user}                                 |
      | token    | usert0k3n                              |
      | scope    | {\Dothiv\ValueObject\IdentValue@login} |
      | lifetime | {\DateTime@2014-01-02T13:44:15}        |
    And the "DothivBusinessBundle:Registrar" entity exists in "registrar" with values:
      | extId | 1234-AC        |
      | name  | ACME Registrar |
    And the "DothivBusinessBundle:Domain" entity exists in "domain" with values:
      | registrar | {registrar} |
      | name      | test.hiv    |
      | owner     | {user}      |
    And I add "Accept" header equal to "application/json"
    And I add "Content-Type" header equal to "application/json"
    And I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"

  Scenario: Add / remove collaborators
    Given I send a POST request to "http://click4life.hiv.dev/api/domain/test.hiv/collaborator" with JSON values:
      | email     | jane.doe@example.com |
      | firstname | Jane                 |
      | lastname  | Doe                  |
    Then the response status code should be 201
    And the header "Location" should exist
    And the header "Location" is stored in "collaboratorUrl"
    Given I send a GET request to "http://click4life.hiv.dev/api/domain/test.hiv/collaborator"
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "items" should contain 1 elements
    And the JSON node "items[0].user.email" should be equal to "jane.doe@example.com"
    And the JSON node "items[0].user.firstname" should be equal to "Jane"
    And the JSON node "items[0].user.surname" should be equal to "Doe"
    # FIXME: Implement
    #Given I send a DELETE request to {collaboratorUrl}
    #Then the response status code should be 204
    #Given I send a GET request to "http://click4life.hiv.dev/api/domain/test.hiv/collaborator"
    #And the JSON node "items" should contain 0 elements
