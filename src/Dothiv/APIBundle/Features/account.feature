Feature: User Account
  A user should be able fetch his account information

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "user" with values:
      | handle    | userhandle          |
      | email     | someone@example.com |
      | firstname | John                |
      | surname   | Doe                 |
    Given the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user     | {user}                                 |
      | token    | usert0k3n                              |
      | scope    | {\Dothiv\ValueObject\IdentValue@login} |
      | lifetime | {\DateTime@2014-01-02T13:44:15}        |
    And I add "Accept" header equal to "application/json"

  Scenario: Fetch User Info
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a GET request to "http://click4life.hiv.dev/api/user/userhandle"
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "email" should contain "someone@example.com"
    And the JSON node "firstname" should contain "John"
    And the JSON node "surname" should contain "Doe"
