@User @Password @PasswordRecover
Feature: Recover password
  As a user
  I should be able to recover my password

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "user" with values:
      | handle    | userhandle          |
      | email     | someone@example.com |
      | firstname | John                |
      | surname   | Doe                 |
    And the password of "user" is set to "examplepass"
    And the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user     | {user}                               |
      | token    | usert0k3n                            |
      | scope    | {\Dothiv\ValueObject\IdentValue@api} |
      | lifetime | {\DateTime@2014-01-02T13:44:15}      |
    And I add "Accept" header equal to "application/json"

  Scenario: Request a new password
    When I send a POST request to "http://click4life.hiv.dev/api/recoverpassword" with JSON values:
      | email    | someone@example.com |
      | password | newpass             |
    Then the response status code should be 204
    # The password is NOT updated
    When I send a POST request to "http://someone@example.com:newpass@click4life.hiv.dev/api/usertoken"
    Then the response status code should be 401
    # An email with a confirmation link is sent to the user
    Given "unconfirmedChanges" contains the result of calling "findByUser" on the "dothiv.repository.user_profile_change" service with values:
      | {user} |
    And I build "unconfirmedChangeUrl" from "http://click4life.hiv.dev/api/user/{unconfirmedChanges[0].user.publicId}/profile_change/{unconfirmedChanges[0].publicId}"
    And I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    # The email contains the token he uses to confirm the change
    When I send a PATCH request to "{unconfirmedChangeUrl}" with JSON values:
      | confirmed | {unconfirmedChanges[0].token} |
    Then the response status code should be 204
    Given I add Bearer token equal to ""
    # The password should be updated
    When I send a POST request to "http://someone@example.com:newpass@click4life.hiv.dev/api/usertoken"
    Then the response status code should be 201

  Scenario: Try to reset password for non-existent email
    Given I send a POST request to "http://click4life.hiv.dev/api/recoverpassword" with JSON values:
      | email    | Jane.Doe@example.com |
      | password | newpass              |
    # Note: The correct error code would be 404 or 400, but an attacker must not be able to guess registered email addresses
    Then the response status code should be 204
    Given "unconfirmedChanges" contains the result of calling "findByUser" on the "dothiv.repository.user_profile_change" service with values:
      | {user} |
    Then "{unconfirmedChanges}" should contain 0 elements

  Scenario: Rate limit reset password attempts
    Given I send a POST request to "http://click4life.hiv.dev/api/recoverpassword" with JSON values:
      | email    | someone@example.com |
      | password | newpass             |
    Then the response status code should be 204
    Given I send a POST request to "http://click4life.hiv.dev/api/recoverpassword" with JSON values:
      | email    | someone@example.com |
      | password | newpass             |
    Then the response status code should be 204
    Given "unconfirmedChanges" contains the result of calling "findByUser" on the "dothiv.repository.user_profile_change" service with values:
      | {user} |
    And "{unconfirmedChanges}" should contain 1 element
