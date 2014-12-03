@UserNotification
Feature: See user notifications
  As user should be able see notifications for them

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "userA" with values:
      | handle    | userAhandle         |
      | email     | someone@example.com |
      | firstname | John                |
      | surname   | Doe                 |
    Given the "DothivBusinessBundle:User" entity exists in "userB" with values:
      | handle    | userBhandle         |
      | email     | someone@example.net |
      | firstname | Jane                |
      | surname   | Doe                 |
    Given the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user     | {userA}                                      |
      | token    | usert0k3n                                    |
      | scope    | {\Dothiv\ValueObject\IdentValue@domainclaim} |
      | lifetime | {\DateTime@2014-01-02T13:44:15}              |
    And the "DothivBusinessBundle:UserNotification" entity exists in "notificationA" with values:
      | user       | {userA}                   |
      | properties | {\array@{"key": "value"}} |
    And the "DothivBusinessBundle:UserNotification" entity exists in "notificationB" with values:
      | user       | {userB}                   |
      | properties | {\array@{"key": "value"}} |
    And I add "Accept" header equal to "application/json"
    And I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"

  Scenario: List unread notifications
    Given I send a GET request to "http://click4life.hiv.dev/api/user/userAhandle/notification"
    Then the response status code should be 200
    And the header "content-type" should contain "application/json"
    And the JSON node "items" should contain 1 elements

  Scenario: Mark notification read
    Given I send a PATCH request to "http://click4life.hiv.dev/api/user/userAhandle/notification/1" with JSON values:
      | dismissed | 1 |
    Then the response status code should be 204
    # The notification should be updated
    When I send a GET request to "http://click4life.hiv.dev/api/user/userAhandle/notification/1"
    Then the JSON node "dismissed" should contain "1"
    # Should not show up
    When I send a GET request to "http://click4life.hiv.dev/api/user/userAhandle/notification"
    Then the JSON node "items" should not exist
    # Unmark
    Given I send a PATCH request to "http://click4life.hiv.dev/api/user/userAhandle/notification/1" with JSON values:
      | dismissed | 0 |
    Then the response status code should be 204
    # The notification should be updated
    When I send a GET request to "http://click4life.hiv.dev/api/user/userAhandle/notification/1"
    Then the JSON node "dismissed" should contain "0"
