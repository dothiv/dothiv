@PremiumConfigurator @Subscription
Feature: Create subscription
  A user should be able to create a subscription for a premium banner

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "user" with values:
      | handle    | userhandle          |
      | email     | someone@example.com |
      | firstname | John                |
      | surname   | Doe                 |
    Given the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user     | {user}                          |
      | token    | usert0k3n                       |
      | lifetime | {\DateTime@2014-01-02T13:44:15} |
    And the "DothivBusinessBundle:Domain" entity exists in "domain" with values:
      | name  | test.hiv |
      | owner | {user}   |
    And I add "Accept" header equal to "application/json"

  Scenario: Initially, no subscription should exist
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a GET request to "http://click4life.hiv.dev/api/premium-configurator/test.hiv/subscription"
    Then the response status code should be 404

  Scenario: Purchase subscription
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a PUT request to "http://click4life.hiv.dev/api/premium-configurator/test.hiv/subscription" with JSON values:
      | token    | tok_14O6xC42KFPpMZB0Q9FRb662 |
      | liveMode | true                         |
    Then the response status code should be 200
    And I send a GET request to "http://click4life.hiv.dev/api/premium-configurator/test.hiv/subscription"
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "liveMode" should contain "1"

  Scenario: Purchase subscription for invalid domain
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a PUT request to "http://click4life.hiv.dev/api/premium-configurator/invalid.hiv/subscription" with JSON values:
      | token    | tok_14O6xC42KFPpMZB0Q9FRb662 |
      | livemode | true                         |
    Then the response status code should be 404

  Scenario: Purchase subscription for other users domain
    Given I add Bearer token equal to "bla"
    And I send a PUT request to "http://click4life.hiv.dev/api/premium-configurator/test.hiv/subscription" with JSON values:
      | token    | tok_14O6xC42KFPpMZB0Q9FRb662 |
      | livemode | true                         |
    Then the response status code should be 403
