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

  Scenario: I can delete a user
    Given I am on "admin"
    When I press "Delete" for "damien" profile
    Then I should see "The user has been deleted"
    And I should see the users "lilith"
