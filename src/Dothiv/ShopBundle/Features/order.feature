@Shop @DomainOrder
Feature: Order domain
  As a user
  I should be able to order a domain name

  Background:
    Given I add "Accept" header equal to "application/json"

  Scenario: Order domain
    And I send a PUT request to "https://tld.hiv.dev/api/shop/order/xn--brger-kva.hiv" with JSON values:
      | clickcounter | 1                            |
      | redirect     | http://jana.com/             |
      | duration     | 3                            |
      | firstname    | Jana                         |
      | lastname     | Bürger                       |
      | email        | jana.müller@bürger.de        |
      | phone        | +49301234567                 |
      | fax          | +4930123456777               |
      | locality     | Waldweg 1                    |
      | locality2    | Hinterhaus                   |
      | city         | 12345 Neustadt               |
      | country      | Germany (Deutschland)        |
      | organization | Bürger GmbH                  |
      | vat          | DE123456789                  |
      | stripe_token | tok_14kvt242KFPpMZB00CUopZjt |
      | stripe_card  | crd_14kvt242KFPpMZB00CUopZjt |
    Then the response status code should be 201
    And the header "content-type" should contain "application/json"
    And the JSON node "@context" should contain "http://jsonld.click4life.hiv/HivDomainOrder"
