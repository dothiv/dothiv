@Login
Feature: Login
  A user should be able to request a login link

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "user" with values:
      | handle    | userhandle          |
      | email     | someone@example.com |
      | firstname | John                |
      | surname   | Doe                 |
    Given I add "Accept" header equal to "application/json"
    And I add "Content-Type" header equal to "application/json"

  Scenario: Request login link
    And I send a POST request to "http://click4life.hiv.dev/api/account/loginLink" with JSON values:
      | email  | SomeOne@Example.Com |
      | locale | en                  |
    Then the response status code should be 201
    # Second login link should not be created
    Given I send a POST request to "http://click4life.hiv.dev/api/account/loginLink" with JSON values:
      | email  | someone@example.com |
      | locale | en                  |
    Then the response status code should be 429
    And the header "Retry-After" should be equal to "3600"

  Scenario: Request login link after token lifetime exceeded
    Given the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user     | {user}                                                |
      | token    | usert0k3n                                             |
      | scope    | {\Dothiv\BusinessBundle\ValueObject\IdentValue@login} |
      | lifetime | {\DateTime@2013-12-31T23:59:59}                       |
    And I send a POST request to "http://click4life.hiv.dev/api/account/loginLink" with JSON values:
      | email  | SomeOne@Example.Com |
      | locale | en                  |
    Then the response status code should be 201

  Scenario: Request login link after token revoked
    Given the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user        | {user}                                                |
      | token       | usert0k3n                                             |
      | scope       | {\Dothiv\BusinessBundle\ValueObject\IdentValue@login} |
      | lifetime    | {\DateTime@2015-01-01T00:00:00}                       |
      | revokedTime | {\DateTime@2013-12-31T23:59:59}                       |
    And I send a POST request to "http://click4life.hiv.dev/api/account/loginLink" with JSON values:
      | email  | SomeOne@Example.Com |
      | locale | en                  |
    Then the response status code should be 201

  Scenario: Request login token regardless of existing domainclaim token
    Given the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user     | {user}                                                      |
      | token    | cl4imt0k3n                                                  |
      | scope    | {\Dothiv\BusinessBundle\ValueObject\IdentValue@domainclaim} |
      | lifetime | {\DateTime@2014-01-02T13:44:15}                             |
    And I send a POST request to "http://click4life.hiv.dev/api/account/loginLink" with JSON values:
      | email  | SomeOne@Example.Com |
      | locale | en                  |
    Then the response status code should be 201

  Scenario: Request new login token every 60 minutes, regardless of token lifetime
    Given the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user     | {user}                                                |
      | token    | usert0k3n                                             |
      | scope    | {\Dothiv\BusinessBundle\ValueObject\IdentValue@login} |
      # Clock is "2014-01-02T13:14:15"
      # Lives 14 days
      | lifetime | {\DateTime@2014-01-16T13:14:14}                       |
      # Created 60mins ago
      | created  | {\DateTime@2014-01-02T12:14:14}                       |
    And I send a POST request to "http://click4life.hiv.dev/api/account/loginLink" with JSON values:
      | email  | SomeOne@Example.Com |
      | locale | en                  |
    Then the response status code should be 201
