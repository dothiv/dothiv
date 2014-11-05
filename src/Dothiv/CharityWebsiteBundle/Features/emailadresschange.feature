@Account @Email
Feature: Change the email address for a domain account
  User user should be able to change their email adress 
  if the account was generated from WHOIS data

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
    And I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"

  Scenario: See notification about possible email change
    Given I send a GET request to "http://click4life.hiv.dev/api/user/userhandle/notification"
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "items" should contain 1 elements
