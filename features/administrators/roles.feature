Feature: Manage a user account
  In order to administrate user accounts
  As an administrator
  I need to interact with the administration page

  Background:
    Given I am on the homepage
    And I am anonymous
    And I go to "login"
    And I fill in "Username" with "aurore"
    And I fill in "Password" with "aurore"
    And I press "Log in"

  Scenario: I can change a user role
    Given I am on "admin"
    When I follow "Change role" for "damien" profile
    And I select "Editor" from "Roles"
    And I press "Change"
    Then I should see "User role has been changed"
    And user "damien" should have role "ROLE_EDITOR"
