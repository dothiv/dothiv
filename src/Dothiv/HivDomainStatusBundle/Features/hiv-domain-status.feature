@AdminBundle @HivDomainStatus
Feature: .hiv Domain Status
  An admin
  I should be able to view the .hiv domain status

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "admin" with values:
      | handle    | adminhandle              |
      | email     | admin@click4life.hiv.dev |
      | firstname | John                     |
      | surname   | Doe                      |
    And the "DothivBusinessBundle:UserToken" entity exists in "adminToken" with values:
      | user     | {admin}                                |
      | token    | admint0k3n                             |
      | lifetime | {\DateTime@2015-01-01T00:00:00}        |
      | scope    | {\Dothiv\ValueObject\IdentValue@login} |
    And the "DothivBusinessBundle:Registrar" entity exists in "registrar" with values:
      | extId | 1234-AC        |
      | name  | ACME Registrar |
    And the "DothivBusinessBundle:Domain" entity exists in "domain" with values:
      | registrar  | {registrar}  |
      | name       | test.hiv     |
      | token      | domaint0k3n  |
      | ownerEmail | john@doe.com |
      | ownerName  | John Doe     |
    And the "DothivHivDomainStatusBundle:HivDomainCheck" entity exists in "check1" with values:
      | domain | {domain}            |
      | url    | http://example.com/ |
    And the "DothivHivDomainStatusBundle:HivDomainCheck" entity exists in "check2" with values:
      | domain | {domain}            |
      | url    | http://example.com/ |
      | valid  | true                |
    Given I add "Accept" header equal to "application/json"
    And I add "Content-Type" header equal to "application/json"
    And I add Bearer token equal to "3e11fe85b5c5522aedc52015c21b6c1fda3cc4b4"

  Scenario: Request the check status for the domain
    And I send a GET request to "http://tld.hiv.dev/admin/api/hivdomainstatus/test.hiv"
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "url" should contain "http://example.com/"
    And the JSON node "statusCode" should be equal to "0"
    And the JSON node "scriptPresent" should be equal to false
    And the JSON node "iframePresent" should be equal to false
    And the JSON node "iframeTargetOk" should be equal to false
    And the JSON node "valid" should be equal to true
