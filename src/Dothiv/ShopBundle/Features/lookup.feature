@Shop @DomainLookup
Feature: Lookup domain name
  As a user
  I should be able to lookup domain names I am interested in registering

  Background:
    Given the "DothivBusinessBundle:DomainInfo" entity exists in "registeredDomain" with values:
      | name       | {\Dothiv\ValueObject\HivDomainValue@cto.hiv} |
      | registered | 1                                            |
    Given the "DothivBusinessBundle:DomainInfo" entity exists in "premiumDomain" with values:
      | name    | {\Dothiv\ValueObject\HivDomainValue@click.hiv} |
      | premium | 1                                              |
    Given the "DothivBusinessBundle:DomainInfo" entity exists in "blockedDomain" with values:
      | name    | {\Dothiv\ValueObject\HivDomainValue@google.hiv} |
      | blocked | 1                                               |
    Given the "DothivBusinessBundle:DomainInfo" entity exists in "trademarkDomain" with values:
      | name      | {\Dothiv\ValueObject\HivDomainValue@facebook.hiv} |
      | trademark | 1                                                 |
    Given the "DothivBusinessBundle:Config" entity exists in "usdPrice" with values:
      | name  | shop.price.usd |
      | value | 18000          |
    Given the "DothivBusinessBundle:Config" entity exists in "eurPrice" with values:
      | name  | shop.price.eur |
      | value | 14500          |
    Given the "DothivBusinessBundle:Config" entity exists in "eurPriceMod" with values:
      | name  | shop.promo.name4life.eur.mod |
      | value | -13000                       |
    Given the "DothivBusinessBundle:Config" entity exists in "usdPriceMod" with values:
      | name  | shop.promo.name4life.usd.mod |
      | value | -16100                       |
    And I add "Accept" header equal to "application/json"

  Scenario: Lookup available domain
    And I send a GET request to "https://tld.hiv.dev/api/shop/lookup" with query:
      | q | caro.hiv |
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "@context" should contain "http://jsonld.click4life.hiv/DomainInfo"
    And the JSON node "registered" should be equal to false
    And the JSON node "premium" should be equal to false
    And the JSON node "blocked" should be equal to false
    And the JSON node "trademark" should be equal to false
    And the JSON node "available" should be equal to true
    And the JSON node "netPriceUSD" should contain "18000"
    And the JSON node "netPriceEUR" should contain "14500"

  Scenario: Lookup invalid domain
    And I send a GET request to "https://tld.hiv.dev/api/shop/lookup" with query:
      | q | t.hiv |
    Then the response status code should be 400
    And the header "content-type" should contain "application/json+problem"
    And the JSON node "@context" should contain "http://ietf.org/appsawg/http-problem"
    And the JSON node "title" should be equal to "Invalid hiv domain provided: "t.hiv"!"

  # xn--mgb9awbf6b.hiv // عُمان (oman)
  Scenario: Lookup invalid domain
    And I send a GET request to "https://tld.hiv.dev/api/shop/lookup" with query:
      | q | xn--mgb9awbf6b.hiv |
    Then the response status code should be 400
    And the header "content-type" should contain "application/json+problem"
    And the JSON node "@context" should contain "http://ietf.org/appsawg/http-problem"
    And the JSON node "title" should be equal to "hiv domain name contains invalid characters: "xn--mgb9awbf6b.hiv"!"

  Scenario: Lookup available promo domain
    And I send a GET request to "https://tld.hiv.dev/api/shop/lookup" with query:
      | q | caro4life.hiv |
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "@context" should contain "http://jsonld.click4life.hiv/DomainInfo"
    And the JSON node "registered" should be equal to false
    And the JSON node "premium" should be equal to false
    And the JSON node "blocked" should be equal to false
    And the JSON node "trademark" should be equal to false
    And the JSON node "available" should be equal to true
    And the JSON node "netPriceUSD" should contain "1900"
    And the JSON node "netPriceEUR" should contain "1500"

  Scenario: Lookup registered domain
    And I send a GET request to "https://tld.hiv.dev/api/shop/lookup" with query:
      | q | cto.hiv |
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "@context" should contain "http://jsonld.click4life.hiv/DomainInfo"
    And the JSON node "registered" should be equal to true
    And the JSON node "premium" should be equal to false
    And the JSON node "blocked" should be equal to false
    And the JSON node "trademark" should be equal to false
    And the JSON node "available" should be equal to false
    And the JSON node "netPriceUSD" should not exist
    And the JSON node "netPriceEUR" should not exist

  Scenario: Lookup premium domain
    And I send a GET request to "https://tld.hiv.dev/api/shop/lookup" with query:
      | q | click.hiv |
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "@context" should contain "http://jsonld.click4life.hiv/DomainInfo"
    And the JSON node "registered" should be equal to false
    And the JSON node "premium" should be equal to true
    And the JSON node "blocked" should be equal to false
    And the JSON node "trademark" should be equal to false
    And the JSON node "available" should be equal to false
    And the JSON node "netPriceUSD" should not exist
    And the JSON node "netPriceEUR" should not exist

  Scenario: Lookup name collision domain
    And I send a GET request to "https://tld.hiv.dev/api/shop/lookup" with query:
      | q | google.hiv |
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "@context" should contain "http://jsonld.click4life.hiv/DomainInfo"
    And the JSON node "registered" should be equal to false
    And the JSON node "premium" should be equal to false
    And the JSON node "blocked" should be equal to true
    And the JSON node "trademark" should be equal to false
    And the JSON node "available" should be equal to false
    And the JSON node "netPriceUSD" should not exist
    And the JSON node "netPriceEUR" should not exist

  Scenario: Lookup name trademark domain
    And I send a GET request to "https://tld.hiv.dev/api/shop/lookup" with query:
      | q | facebook.hiv |
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "@context" should contain "http://jsonld.click4life.hiv/DomainInfo"
    And the JSON node "registered" should be equal to false
    And the JSON node "premium" should be equal to false
    And the JSON node "blocked" should be equal to false
    And the JSON node "trademark" should be equal to true
    And the JSON node "available" should be equal to false
    And the JSON node "netPriceUSD" should not exist
    And the JSON node "netPriceEUR" should not exist
