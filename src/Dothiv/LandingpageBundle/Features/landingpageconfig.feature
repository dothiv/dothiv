@Landingpage @LandingpageConfig
Feature: Order domain
  As an owner of a 4life.hiv domain name
  I should be able to configure my landingpage

  Background:
    Given the fixture "\Dothiv\LandingpageBundle\Tests\Fixtures\LandingpagePreviewTestFixture" is loaded
    And I add "Accept" header equal to "application/json"
    And I add "Content-Type" header equal to "application/json"
    And I add Bearer token equal to "e6355e81a89d75f5b23f74a123d6594c652cb0d0"

  Scenario: Fetch configuration of my domain
    Given I send a GET request to "https://click4life.hiv.dev/api/landingpage/caro4life.hiv"
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "name" should contain "Caro"
    And the JSON node "language" should contain "en"
    And the JSON node "text" should not exist
    And the JSON node "clickCounter" should be equal to true

  Scenario: Update configuration of my domain
    And I send a PATCH request to "https://click4life.hiv.dev/api/landingpage/caro4life.hiv" with JSON values:
      | name     | Mike      |
      | text     | Some Text |
      | language | es        |
    Then I debug the JSON
    Then the response status code should be 204
    Given I send a GET request to "https://click4life.hiv.dev/api/landingpage/caro4life.hiv"
    And the header "content-type" should contain "application/json"
    And the JSON node "name" should contain "Mike"
    And the JSON node "language" should contain "es"
    And the JSON node "text" should contain "Some Text"
    And the JSON node "clickCounter" should be equal to true

  Scenario: Fetch configuration of someone else's domain
    Given I send a GET request to "https://click4life.hiv.dev/api/landingpage/polly4life.hiv"
    Then the response status code should be 403

  Scenario: Fetch configuration of someone unknown domain
    Given I send a GET request to "https://click4life.hiv.dev/api/landingpage/noop4life.hiv"
    Then the response status code should be 404
