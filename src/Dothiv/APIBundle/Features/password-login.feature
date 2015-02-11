@User @Password @PasswordLogin
Feature: Password login
  As an user
  I should be able to log in with my email and a password

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "user" with values:
      | handle    | userhandle          |
      | email     | someone@example.com |
      | firstname | John                |
      | surname   | Doe                 |
    And the password of "user" is set to "examplepass"
    And I add "Accept" header equal to "application/json"

  Scenario: Create new API token
    When I send a POST request to "http://click4life.hiv.dev/api/usertoken" with JSON values:
      | email    | someone@example.com |
      | password | examplepass         |
    Then the response status code should be 201
    And the header "content-type" should contain "application/json"
    And the JSON node "@context" should contain "http://jsonld.click4life.hiv/UserToken"
    And the JSON node "scope" should contain "api"
    And the JSON node "lifeTime" should exist

  Scenario: Use invalid password
    When I send a POST request to "http://click4life.hiv.dev/api/usertoken" with JSON values:
      | email    | someone@example.com |
      | password | wrongpass           |
    Then the response status code should be 400

  Scenario: Use invalid email
    # Note: The correct error code would be 404, but an attacker must not be able to guess registered email addresses
    When I send a POST request to "http://click4life.hiv.dev/api/usertoken" with JSON values:
      | email    | someoneelse@example.com |
      | password | examplepass             |
    Then the response status code should be 400
