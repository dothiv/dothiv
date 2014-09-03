@PremiumBid
Feature: Confirm Sedo Bid
  A user I should be able to register a sedo bid

  Scenario: Create bid
    And I send a POST request to "http://tld.hiv.dev/api/premiumbid" with JSON values:
      | name      | example.hiv |
      | firstname | John        |
      | surname   | Doe         |
    Then the response status code should be 201
    And "bids" contains the result of calling "getUnnotified" on the "dothiv.repository.premiumbid" service
    And {bids} should be a list with 1 element
