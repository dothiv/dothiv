@Payitforward @Order
Feature: Order payitforward vouchers
  As a user
  I should be able to order payitforward vouochers

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
    And I add "Accept" header equal to "application/json"

  Scenario: Order three vouchers
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a PUT request to "http://tld.hiv.dev/api/payitforward/order" with JSON values:
      | firstname          | John                         |
      | surname            | Doe                          |
      | email              | john.doe@example.com         |
      | domain             | example.hiv                  |
      | domainDonor        | Some Friend                  |
      | domainDonorTwitter | @friend                      |
      | type               | deorg                        |
      | fullname           | John Doe                     |
      | address1           | 123 Some Street              |
      | address2           | 123 Some City                |
      | country            | Germany (Deutschland)        |
      | vatNo              | 1243                         |
      | taxNo              | 45678                        |
      | domain1            | super.hiv                    |
      | domain1Name        | Super User                   |
      | domain1Company     | Super Company                |
      | domain1Twitter     | @super                       |
      | domain2            | awesome.hiv                  |
      | domain2Name        | Awesome User                 |
      | domain2Company     | Awesome Company              |
      | domain2Twitter     | @awesome                     |
      | domain3            | rad.hiv                      |
      | domain3Name        | Rad User                     |
      | domain3Company     | Rad Company                  |
      | domain3Twitter     | @rad                         |
      | token              | tok_14kcI342KFPpMZB0scN8KPTM |
      | liveMode           | 0                            |
    Then the response status code should be 201


