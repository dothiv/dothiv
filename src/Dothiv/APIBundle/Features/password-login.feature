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
    When I send a POST request to "http://someone@example.com:examplepass@click4life.hiv.dev/api/usertoken"
    Then the response status code should be 201
    And the header "content-type" should contain "application/json"
    And the JSON node "@context" should contain "http://jsonld.click4life.hiv/UserToken"
    And the JSON node "scope" should contain "api"
    And the JSON node "lifeTime" should contain "2014-01-02T13:44:15+01:00"

  Scenario: Use invalid password
    When I send a POST request to "http://someone@example.com:wrongpass@click4life.hiv.dev/api/usertoken"
    Then the response status code should be 401

  Scenario: Use invalid email
    # Note: The correct error code would be 404, but an attacker must not be able to guess registered email addresses
    When I send a POST request to "http://someoneelse@example.com:examplepass@click4life.hiv.dev/api/usertoken"
    Then the response status code should be 401
