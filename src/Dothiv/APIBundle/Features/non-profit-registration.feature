@APIBundle @NonProfitRegistration
Feature: Non-Profit Registration
  A user should be able create a non-profit registration

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "user" with values:
      | handle    | userhandle          |
      | email     | someone@example.com |
      | firstname | John                |
      | surname   | Doe                 |
    And the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user     | {user}                          |
      | token    | usert0k3n                       |
      | lifetime | {\DateTime@2014-01-02T13:44:15} |
    And the "DothivBusinessBundle:Attachment" entity exists in "attachment" with values:
      | user      | {user}                           |
      | handle    | ad54af9f3a2e137d04588712e3d98e0d |
      | mimeType  | application/pdf                  |
      | extension | pdf                              |
    And I add "Accept" header equal to "application/json"
    And I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"

  Scenario: Get registration
    Given I send a GET request to "/api/nonprofit/example.hiv"
    Then the response status code should be 404

  Scenario: Create Registration
    Given I send a PUT request to "/api/nonprofit/example.hiv" with JSON values:
      | personFirstname | Jill                             |
      | personSurname   | Jones                            |
      | personEmail     | jill@example.com                 |
      | personPosition  | CEO                              |
      | organization    | ACME Inc.                        |
      | proof           | ad54af9f3a2e137d04588712e3d98e0d |
      | about           | ACME Stuff                       |
      | field           | prevention                       |
      | postcode        | 12345                            |
      | locality        | Big City                         |
      | country         | United States                    |
      | website         | http://example.com/              |
      | concept         | I have this idea …               |
      | forward         | 1                                |
      | terms           | 1                                |
      | personPhone     | +49178451                        |
      | personFax       | +49178452                        |
      | orgPhone        | +49178453                        |
      | orgFax          | +49178454                        |
    Then the response status code should be 201
    And the header "content-type" should contain "application/json"
    And the JSON node "domain" should contain "example.hiv"
    And the JSON node "personFirstname" should contain "Jill"
    And the JSON node "personSurname" should contain "Jones"
    And the JSON node "personEmail" should contain "jill@example.com"
    And the JSON node "personPosition" should contain "CEO"
    And the JSON node "organization" should contain "ACME Inc."
    And the JSON node "about" should contain "ACME Stuff"
    And the JSON node "field" should contain "prevention"
    And the JSON node "postcode" should contain "12345"
    And the JSON node "locality" should contain "Big City"
    And the JSON node "country" should contain "United States"
    And the JSON node "website" should contain "http://example.com/"
    And the JSON node "concept" should contain "I have this idea …"
    And the JSON node "forward" should contain "1"
    And the JSON node "personPhone" should contain "+49178451"
    And the JSON node "personFax" should contain "+49178452"
    And the JSON node "orgPhone" should contain "+49178453"
    And the JSON node "orgFax" should contain "+49178454"

  Scenario: Create Registration without forward
    Given I send a PUT request to "/api/nonprofit/example2.hiv" with JSON values:
      | personFirstname | Jill                             |
      | personSurname   | Jones                            |
      | personEmail     | jill@example.com                 |
      | personPosition  | CEO                              |
      | organization    | ACME Inc.                        |
      | proof           | ad54af9f3a2e137d04588712e3d98e0d |
      | about           | ACME Stuff                       |
      | field           | prevention                       |
      | postcode        | 12345                            |
      | locality        | Big City                         |
      | country         | United States                    |
      | website         | http://example.com/              |
      | concept         | I have this idea …               |
      | terms           | 1                                |
      | personPhone     | +49178451                        |
      | personFax       | +49178452                        |
      | orgPhone        | +49178453                        |
      | orgFax          | +49178454                        |
    Then the response status code should be 201
    And the header "content-type" should contain "application/json"
    And the JSON node "domain" should contain "example2.hiv"
    And the JSON node "forward" should contain "0"
