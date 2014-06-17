Feature: Pinkbar API
  The current click count should be available via a REST api

  Scenario: Fetch current click count
    Given I add "Accept" header equal to "application/json"
    And I send a GET request to "http://click4life.hiv.dev/en/pinkbar"
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "clicks" should be equal to "0"
    And the JSON node "enabled" should be equal to "0"
