@User @EmailChange
Feature: Change email address
  As user I should be able to change my email address

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "userA" with values:
      | handle    | userAhandle         |
      | email     | someone@example.com |
      | firstname | John                |
      | surname   | Doe                 |
    Given the "DothivBusinessBundle:User" entity exists in "userB" with values:
      | handle    | userBhandle          |
      | email     | Jane.Doe@example.com |
      | firstname | Jane                 |
      | surname   | Doe                  |
    Given the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user     | {userA}                                      |
      | token    | usert0k3n                                    |
      | scope    | {\Dothiv\ValueObject\IdentValue@domainclaim} |
      | lifetime | {\DateTime@2014-01-02T13:44:15}              |
    And the "DothivBusinessBundle:Registrar" entity exists in "registrar" with values:
      | extId | 1234-AC        |
      | name  | ACME Registrar |
    And the "DothivBusinessBundle:Domain" entity exists in "domain" with values:
      | registrar | {registrar} |
      | name      | test.hiv    |
      | owner     | {userA}      |
    And I add "Accept" header equal to "application/json"
    And I add "Accept-Language" header equal to "de;q=0.9,en-US,en;q=0.8"
    And I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"

  Scenario: Request a profile change
    Given I send a PATCH request to "http://click4life.hiv.dev/api/user/userAhandle" with JSON values:
      | email | someoneelse@example.com |
    Then the response status code should be 201
    And the header "Location" should exist
    Given the header "Location" is stored in "unconfirmedChangeUrl"
    # The email should not (!) be updated
    When I send a GET request to "http://click4life.hiv.dev/api/user/userAhandle"
    Then the JSON node "email" should contain "someone@example.com"
    Given "unconfirmedChanges" contains the result of calling "findByUser" on the "dothiv.repository.user_profile_change" service with values:
      | {userA} |
    When I send a PATCH request to "{unconfirmedChangeUrl}" with JSON values:
      | confirmed | {unconfirmedChanges[0].token} |
    Then the response status code should be 204
    # The email should be updated
    When I send a GET request to "http://click4life.hiv.dev/api/user/userAhandle"
    Then the JSON node "email" should contain "someoneelse@example.com"
    # The domain owner email should be updated
    Given "updatedDomain" contains the result of calling "findOneByName" on the "dothiv.repository.domain" service with values:
      | test.hiv |
    Then "{updatedDomain.ownerEmail}" should contain "someoneelse@example.com"

  Scenario: Try to change profile email to exisiting email
    Given I send a PATCH request to "http://click4life.hiv.dev/api/user/userAhandle" with JSON values:
      | email | Jane.Doe@example.com |
    Then the response status code should be 409
