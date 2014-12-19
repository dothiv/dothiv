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
      | vatNo        | DE123456789                  |
      | currency     | EUR                          |
      | stripeToken  | tok_14kvt242KFPpMZB00CUopZjt |
      | stripeCard   | crd_14kvt242KFPpMZB00CUopZjt |
    Then the response status code should be 201
    And the header "content-type" should contain "application/json"
    # The order should be created
    Given "order" contains the result of calling "findOneByDomain" on the "dothiv.repository.shop_order" service with values:
      | xn--brger-kva.hiv |
    Then "{order.email}" should contain "jana.müller@bürger.de"
    And "{order.Domain}" should contain "xn--brger-kva.hiv"
    And "{order.ClickCounter}" should be equal to true
    And "{order.Redirect}" should contain "http://jana.com/"
    And "{order.Duration}" should contain "3"
    And "{order.Firstname}" should contain "Jana"
    And "{order.Lastname}" should contain "Bürger"
    And "{order.Email}" should contain "jana.müller@bürger.de"
    And "{order.Phone}" should contain "+49301234567"
    And "{order.Fax}" should contain "+4930123456777"
    And "{order.Locality}" should contain "Waldweg 1"
    And "{order.Locality2}" should contain "Hinterhaus"
    And "{order.City}" should contain "12345 Neustadt"
    And "{order.Country}" should contain "Germany (Deutschland)"
    And "{order.Organization}" should contain "Bürger GmbH"
    And "{order.VatNo}" should contain "DE123456789"
    And "{order.Currency}" should contain "EUR"
    And "{order.StripeToken}" should contain "tok_14kvt242KFPpMZB00CUopZjt"
    And "{order.StripeCard}" should contain "crd_14kvt242KFPpMZB00CUopZjt"
    # Ordering twice must not be possible
    When I send a PUT request to "https://tld.hiv.dev/api/shop/order/xn--brger-kva.hiv" with JSON values:
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
      | vatNo        | DE123456789                  |
      | currency     | EUR                          |
      | stripeToken  | tok_24kvt242KFPpMZB00CUopZjt |
      | stripeCard   | crd_24kvt242KFPpMZB00CUopZjt |
    Then the response status code should be 409
