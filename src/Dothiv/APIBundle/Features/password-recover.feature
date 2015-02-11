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
    And I add "Accept" header equal to "application/json"

  Scenario: Request a new password
    Given I send a POST request to "http://click4life.hiv.dev/api/userpassword" with JSON values:
      | email    | someone@example.com |
      | password | newpass             |
    Then the response status code should be 201
    # The password is NOT updated
    When I send a POST request to "http://someone@example.com:newpass@click4life.hiv.dev/api/usertoken"
    Then the response status code should be 400
    # An email with a confirmation link is sent to the user
    Given "unconfirmedChanges" contains the result of calling "findByUser" on the "dothiv.repository.user_profile_change" service with values:
      | {user} |
    # The email contains the token he uses to confirm the change
    When I send a PATCH request to "{unconfirmedChangeUrl}" with JSON values:
      | confirmed | {unconfirmedChanges[0].token} |
    Then the response status code should be 204
    # The password should be updated
    When I send a POST request to "http://someone@example.com:newpass@click4life.hiv.dev/api/usertoken"
    Then the response status code should be 201

  Scenario: Try to reset password for non-existent email
    Given I send a POST request to "http://click4life.hiv.dev/api/userpassword" with JSON values:
      | email    | Jane.Doe@example.com |
      | password | newpass              |
    # Note: The correct error code would be 404, but an attacker must not be able to guess registered email addresses
    Then the response status code should be 400

  Scenario: Rate limit reset password attempts
    Given I send a POST request to "http://click4life.hiv.dev/api/userpassword" with JSON values:
      | email    | someone@example.com |
      | password | newpass             |
    Then the response status code should be 201
    Given I send a POST request to "http://click4life.hiv.dev/api/userpassword" with JSON values:
      | email    | someone@example.com |
      | password | newpass             |
    Then the response status code should be 429
