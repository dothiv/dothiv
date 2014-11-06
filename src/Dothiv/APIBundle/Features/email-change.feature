@User @EmailChange
Feature: Change email address
  As user I should be able to change my email address

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "userA" with values:
      | handle    | userAhandle         |
      | email     | someone@example.com |
      | firstname | John                |
      | surname   | Doe                 |
    Given the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user     | {userA}                                      |
      | token    | usert0k3n                                    |
      | scope    | {\Dothiv\ValueObject\IdentValue@domainclaim} |
      | lifetime | {\DateTime@2014-01-02T13:44:15}              |
    And I add "Accept" header equal to "application/json"
    And I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"

  Scenario: Request a profile change
    Given I send a PATCH request to "http://click4life.hiv.dev/api/user/userAhandle" with JSON values:
      | email | someoneelse@example.com |
    Then the response status code should be 204
    # The email should not (!) be updated
    When I send a GET request to "http://click4life.hiv.dev/api/user/userAhandle"
    And the JSON node "email" should contain "someone@example.com"
    # TODO: Implement confirm
