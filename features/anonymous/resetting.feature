Feature: Reset password
  In order to access the application
  As an anonymous user
  I need to be able to reset my password

  Background:
    Given I am on "login"
    And I am anonymous

  Scenario: I can reset a password by username
    Given I follow "Forgotten password?"
    And I am on "resetting/request"
    When I fill in "Username or email address" with "damien"
    And I press "Reset password"
    Then I should be on "resetting/check-email?username=damien"
    And I should see "An email has been sent. It contains a link you must click to reset your password. Note: You can only request a new password within 24 hours. If you don't get an email check your spam folder or try again."

  Scenario: I can resetting a password by email
    Given I follow "Forgotten password?"
    And I am on "resetting/request"
    When I fill in "Username or email address" with "damien@userbundle.info"
    And I press "Reset password"
    Then I should be on "resetting/check-email?username=damien.carcel%40gmail.com"
    And I should see "An email has been sent. It contains a link you must click to reset your password. Note: You can only request a new password within 24 hours. If you don't get an email check your spam folder or try again."

# Does not work anymore. Issue created: https://github.com/damien-carcel/user-bundle-dev/issues/47
#  Scenario: I fail to reset password if it already is
#    Given I reset "damien" password
#    And I follow "Forgotten password?"
#    And I am on "resetting/request"
#    When I fill in "Username or email address" with "damien"
#    And I press "Reset password"
#    Then I should see "The password for this user has already been requested within the last 24 hours."
#
#  Scenario: Fail to reset a non existing user
#    Given I follow "Forgotten password?"
#    And I am on "resetting/request"
#    When I fill in "Username or email address" with "pandore"
#    And I press "Reset password"
#    Then I should see "The username or email address \"pandore\" does not exist."

  Scenario: I can get back on login page from reset page
    Given I follow "Forgotten password?"
    And I am on "resetting/request"
    When I follow "Back"
    Then I should be on "login"
