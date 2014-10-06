Feature: Logout
  A user should be able to revoke his login token

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
    And I add "Content-Type" header equal to "application/json"

  Scenario: Revoke token
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a DELETE request to "http://click4life.hiv.dev/api/user/userhandle/token"
    Then the response status code should be 200
    # Second revoke should not work
    Given I send a DELETE request to "http://click4life.hiv.dev/api/user/userhandle/token"
    Then the response status code should be 403
