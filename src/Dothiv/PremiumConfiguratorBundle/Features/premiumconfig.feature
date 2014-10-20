@PremiumConfigurator @BannerConfiguration
Feature: Configure Premium Banner
  A user should be able to configure the premium banner for his domain

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
    And the "DothivBusinessBundle:Banner" entity exists in "banner" with values:
      | domain              | {domain}  |
      | language            | fr        |
      | position            | center    |
      | positionAlternative | top       |
      | redirectUrl         | //test.de |
    And I update the "domain" entity with values:
      | activeBanner | {banner} |
    And the "DothivBusinessBundle:Attachment" entity exists in "visual" with values:
      | user      | {user}                           |
      | handle    | de73ec9a8df00d79cd81c937cffa66bb |
      | mimeType  | application/jpeg                 |
      | extension | jpeg                             |
    And the "DothivBusinessBundle:Attachment" entity exists in "bg" with values:
      | user      | {user}                           |
      | handle    | 7d0e009eaa16bba3f7aae0ba670190df |
      | mimeType  | application/png                  |
      | extension | png                              |
    And the "DothivBusinessBundle:Attachment" entity exists in "extrasVisual" with values:
      | user      | {user}                           |
      | handle    | 45472ae3e87c3632c9b7e407b12acd5f |
      | mimeType  | application/jpeg                 |
      | extension | jpeg                             |
    And "visualUrl" contains the result of calling "getUrl" on the "dothiv.premiumconfigurator.service.attachment_store" service with values:
      | {visual} |
    And "bgUrl" contains the result of calling "getUrl" on the "dothiv.premiumconfigurator.service.attachment_store" service with values:
      | {bg} |
    And "extrasVisualUrl" contains the result of calling "getUrl" on the "dothiv.premiumconfigurator.service.attachment_store" service with values:
      | {extrasVisual} |
    And I add "Accept" header equal to "application/json"

  Scenario: Configure Premium Banner
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a PUT request to "https://click4life.hiv.dev/api/premium-configurator/test.hiv/banner" with JSON values:
      | visual             | de73ec9a8df00d79cd81c937cffa66bb |
      | bg                 | 7d0e009eaa16bba3f7aae0ba670190df |
      | fontColor          | #333                             |
      | bgColor            | #f7f7f7                          |
      | barColor           | #e00073                          |
      | headlineFont       | Source Sans Pro                  |
      | headlineFontWeight | 900                              |
      | textFont           | BenchNine                        |
      | textFontWeight     | regular                          |
      | extrasHeadline     | Headline                         |
      | extrasText         | Text                             |
      | extrasLinkUrl      | http://wurst.de/                 |
      | extrasLinkLabel    | Wurst!                           |
      | extrasVisual       | 45472ae3e87c3632c9b7e407b12acd5f |
    Then the response status code should be 200
    And I send a GET request to "https://click4life.hiv.dev/api/premium-configurator/test.hiv/banner"
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "visual" should contain "de73ec9a8df00d79cd81c937cffa66bb"
    And the JSON node "bg" should contain "7d0e009eaa16bba3f7aae0ba670190df"
    And the JSON node "fontColor" should contain "#333"
    And the JSON node "bgColor" should contain "#f7f7f7"
    And the JSON node "barColor" should contain "#e00073"
    And the JSON node "headlineFont" should contain "Source Sans Pro"
    And the JSON node "headlineFontWeight" should contain "900"
    And the JSON node "textFont" should contain "BenchNine"
    And the JSON node "textFontWeight" should contain "regular"
    And the JSON node "extrasHeadline" should contain "Headline"
    And the JSON node "extrasText" should contain "Text"
    And the JSON node "extrasLinkUrl" should contain "http://wurst.de/"
    And the JSON node "extrasLinkLabel" should contain "Wurst!"
    And the JSON node "extrasVisual" should contain "45472ae3e87c3632c9b7e407b12acd5f"
    # Note: different test!
    And the JSON node "@context.visual.url" should contain {visualUrl}
    And the JSON node "@context.bg.url" should contain {bgUrl}
    And the JSON node "@context.extrasVisual.url" should contain {extrasVisualUrl}

  Scenario: Configure Premium Banner for invalid domain
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a PUT request to "https://click4life.hiv.dev/api/premium-configurator/invalid.hiv/banner" with JSON values:
      | extrasText | Text |
    Then the response status code should be 404

  Scenario: Configure Premium Banner for other users domain
    Given I add Bearer token equal to "bla"
    And I send a PUT request to "https://click4life.hiv.dev/api/premium-configurator/test.hiv/banner"
    Then the response status code should be 403
