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

  Scenario: I can see all users on the admin page
    Given I am on "profile"
    When I follow "Administration page"
    Then I should see "Administration of the users"
    And I should see the users "admin, damien and lilith"

  Scenario: A regular user should not be able to access the admin page
    Given I am on "admin"
    And I follow "Log out"
    And I fill in "Username" with "damien"
    And I fill in "Password" with "damien"
    When I press "Log in"
    Then I should see "403 Forbidden"

  Scenario: I can see a user profile
    Given I am on "admin"
    When I follow "View" for "damien" profile
    Then I should see "damien user profile"
    And I should see "Username: damien"
    And I should see "Email: damien@userbundle.info"

  Scenario: I can edit a user profile
    Given I am on "admin"
    When I follow "Edit" for "damien" profile
    Then I should see "Edit user damien profile"
    When I fill in the following:
      | User name     | pandore           |
      | Email address | pandore@gmail.com |
    And I press "Update"
    Then I should see "User profile has been updated"
    And I should see the users "admin, pandore and lilith"

  Scenario: I can change a user role
    Given I am on "admin"
    When I follow "Change role" for "damien" profile
    And I select "Administrator" from "Roles"
    And I press "Change"
    Then I should see "User role has been changed"
    And user "damien" should have role "ROLE_ADMIN"

  Scenario: I can delete a user
    Given I am on "admin"
    When I press "Delete" for "damien" profile
    Then I should see "The user has been deleted"
    And I should see the users "admin and lilith"
