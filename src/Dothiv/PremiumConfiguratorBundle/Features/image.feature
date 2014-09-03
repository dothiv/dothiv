@PremiumConfigurator @ImageUpload
Feature: Image
  A user should be able to upload an image

  Background:
    Given the "DothivBusinessBundle:User" entity exists in "user" with values:
      | handle    | userhandle          |
      | email     | someone@example.com |
      | firstname | John                |
      | surname   | Doe                 |
    And the "DothivBusinessBundle:UserToken" entity exists in "userToken" with values:
      | user     | {user}                                                |
      | token    | usert0k3n                                             |
      | scope    | {\Dothiv\BusinessBundle\ValueObject\IdentValue@login} |
      | lifetime | {\DateTime@2014-01-02T13:44:15}                       |

  Scenario: Upload public file
    Given I add Bearer token equal to "3fa0271a5730ff49539aed903ec981eb1868a735"
    And I send a POST request to "http://click4life.hiv.dev/api/premium-configurator/image" with file "example.png" as "file"
    Then the response status code should be 201
    And the header "content-type" should contain "application/json"
    And the JSON node "handle" should not be empty
    And the header "Location" should exist
    Given the header "Location" is stored in "uploadedImage"
    And I add Bearer token equal to ""
    And I send a GET request to {uploadedImage}
    Then the response status code should be 200
    And the header "content-type" should contain "image/png"
    And the image should be 44x44
