@attachment
Feature: Attachment
  A user should be able create an attachment

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
    And I add "Accept" header equal to "application/json"

  Scenario: Upload file (Non-HTML5 browsers)
    Given I send a POST request to "/api/attachment?auth_token=3fa0271a5730ff49539aed903ec981eb1868a735" with file "example.pdf" as "file"
    Then the response status code should be 201
    And the header "content-type" should contain "application/json"
    And the JSON node "handle" should not be empty

  Scenario: Upload file
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a POST request to "/api/attachment" with file "example.pdf" as "file"
    Then the response status code should be 201
    And the header "content-type" should contain "application/json"
    And the JSON node "handle" should not be empty
