@format @format_eloflexsections @javascript
Feature: Course module indentation in Eloflexsections course format
  In order to reset indentation in course modules
  As a admin
  I want change indent value for all the modules of a course format courses in one go

  Background:
    Given the following "courses" exist:
      | fullname              | shortname | format       |
      | Eloflexsections Course 1 | T1        | eloflexsections |
      | Eloflexsections Course 2 | T2        | eloflexsections |
    And the following "activities" exist:
      | activity | name                       | intro                             | course | idnumber |
      | forum    | Eloflexsections forum name    | Eloflexsections forum description    | T1     | forum1   |
      | data     | Eloflexsections database name | Eloflexsections database description | T1     | data1    |
      | wiki     | Eloflexsections wiki name     | Eloflexsections wiki description     | T2     | wiki1    |
    And I log in as "admin"
    And I am on "Eloflexsections Course 1" course homepage with editing mode on
    And I open "Eloflexsections forum name" actions menu
    And I click on "Move right" "link" in the "Eloflexsections forum name" activity
    And I open "Eloflexsections forum name" actions menu
    And "Move right" "link" in the "Eloflexsections forum name" "activity" should not be visible
    And "Move left" "link" in the "Eloflexsections forum name" "activity" should be visible
    And I press the escape key
    And I open "Eloflexsections database name" actions menu
    And "Move right" "link" in the "Eloflexsections database name" "activity" should be visible
    And "Move left" "link" in the "Eloflexsections database name" "activity" should not be visible
    And I am on "Eloflexsections Course 2" course homepage with editing mode on
    And I open "Eloflexsections wiki name" actions menu
    And I click on "Move right" "link" in the "Eloflexsections wiki name" activity
    And I open "Eloflexsections wiki name" actions menu
    And "Move right" "link" in the "Eloflexsections wiki name" "activity" should not be visible
    And "Move left" "link" in the "Eloflexsections wiki name" "activity" should be visible

  Scenario: Apply course indentation reset for Eloflexsections format
    Given I navigate to "Plugins > Course formats > Elo flexible sections format" in site administration
    And I wait "5" seconds
    And "Reset indentation" "link" should exist
    When I click on "Reset indentation" "link"
    And "Reset indentation" "button" should exist
    And I click on "Reset indentation" "button"
    Then I should see "Indentation reset"
    And I am on "Eloflexsections Course 1" course homepage with editing mode on
    And I open "Eloflexsections forum name" actions menu
    And "Move right" "link" in the "Eloflexsections forum name" "activity" should be visible
    And "Move left" "link" in the "Eloflexsections forum name" "activity" should not be visible
    And I press the escape key
    And I open "Eloflexsections database name" actions menu
    And "Move right" "link" in the "Eloflexsections database name" "activity" should be visible
    And "Move left" "link" in the "Eloflexsections database name" "activity" should not be visible
    And I am on "Eloflexsections Course 2" course homepage with editing mode on
    And I open "Eloflexsections wiki name" actions menu
    And "Move right" "link" in the "Eloflexsections wiki name" "activity" should be visible
    And "Move left" "link" in the "Eloflexsections wiki name" "activity" should not be visible

  Scenario: Cancel course indentation reset for Eloflexsections format
    Given I navigate to "Plugins > Course formats > Elo flexible sections format" in site administration
    And "Reset indentation" "link" should exist
    When I click on "Reset indentation" "link"
    And "Reset indentation" "button" should exist
    And "Cancel" "button" should exist
    And I click on "Cancel" "button"
    Then I should not see "Indentation reset"
    And I am on "Eloflexsections Course 1" course homepage with editing mode on
    And I open "Eloflexsections forum name" actions menu
    And "Move right" "link" in the "Eloflexsections forum name" "activity" should not be visible
    And "Move left" "link" in the "Eloflexsections forum name" "activity" should be visible
    And I press the escape key
    And I open "Eloflexsections database name" actions menu
    And "Move right" "link" in the "Eloflexsections database name" "activity" should be visible
    And "Move left" "link" in the "Eloflexsections database name" "activity" should not be visible
    And I am on "Eloflexsections Course 2" course homepage with editing mode on
    And I open "Eloflexsections wiki name" actions menu
    And "Move right" "link" in the "Eloflexsections wiki name" "activity" should not be visible
    And "Move left" "link" in the "Eloflexsections wiki name" "activity" should be visible
