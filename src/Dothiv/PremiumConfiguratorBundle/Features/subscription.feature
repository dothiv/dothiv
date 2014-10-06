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
      | user     | {user}                                 |
      | token    | usert0k3n                              |
      | scope    | {\Dothiv\ValueObject\IdentValue@login} |
      | lifetime | {\DateTime@2014-01-02T13:44:15}        |
    And the "DothivBusinessBundle:Registrar" entity exists in "registrar" with values:
      | extId | 1234-AC        |
      | name  | ACME Registrar |
    And the "DothivBusinessBundle:Domain" entity exists in "domain" with values:
      | registrar | {registrar} |
      | name      | test.hiv    |
      | owner     | {user}      |
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
      | type     | noneu                        |
      | fullname | John Doe                     |
      | address1 | Street Name                  |
      | address2 | City Name                    |
      | country  | Country Name                 |
      | vatNo    | 123456                       |
      | taxNo    | 456123                       |
    Then the response status code should be 200
    And I send a GET request to "http://click4life.hiv.dev/api/premium-configurator/test.hiv/subscription"
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "liveMode" should contain "1"
    And the JSON node "fullname" should contain "John Doe"
    And the JSON node "address1" should contain "Street Name"
    And the JSON node "address2" should contain "City Name"
    And the JSON node "country" should contain "Country Name"
    And the JSON node "vatNo" should contain "123456"
    And the JSON node "taxNo" should contain "456123"

  Scenario: Purchase subscription for invalid domain
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a PUT request to "http://click4life.hiv.dev/api/premium-configurator/invalid.hiv/subscription" with JSON values:
      | token    | tok_14O6xC42KFPpMZB0Q9FRb662 |
      | livemode | true                         |
      | type     | euprivate                    |
      | fullname | John Doe                     |
      | address1 | Street Name                  |
      | address2 | City Name                    |
      | country  | Country Name                 |
    Then the response status code should be 404

  Scenario: Purchase subscription for other users domain
    Given I add Bearer token equal to "bla"
    And I send a PUT request to "http://click4life.hiv.dev/api/premium-configurator/test.hiv/subscription" with JSON values:
      | token    | tok_14O6xC42KFPpMZB0Q9FRb662 |
      | livemode | true                         |
      | type     | deorg                        |
      | fullname | John Doe                     |
      | address1 | Street Name                  |
      | address2 | City Name                    |
      | country  | Germany (Deutschland)        |
      | vatNo    | 123456                       |
    Then the response status code should be 403
