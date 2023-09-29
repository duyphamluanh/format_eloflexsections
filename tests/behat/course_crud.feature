@format @format_eloflexsections @javascript
Feature: Creating, updating and deleting courses in eloflexsections format
  In order to use eloflexsections format
  As a teacher
  I need to test all basic funtionality

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry     | Teacher  | teacher1@example.com |
      | manager1 | Mary      | Manager  | manager1@example.com |
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
    And the following "system role assigns" exist:
      | user     | role    | contextlevel | reference |
      | manager1 | manager | Category     | CAT1      |

  Scenario: Creating course in eloflexsections format when default format is topics
    When I log in as "manager1"
    And I am on site homepage
    When I press "Add a new course"
    And I set the following fields to these values:
      | Course full name  | My first course          |
      | Course short name | myfirstcourse            |
      | Format            | Elo flexible sections format |
    And I wait to be redirected
    And I expand all fieldsets
    And I set the field "Number of sections" to "5"
    And I press "Save and display"
    And I should see "Topic 1"
    And I should see "Topic 5"
    And I should not see "Topic 6"

  Scenario: Creating course in eloflexsections format when default format is eloflexsections
    Given the following config values are set as admin:
      | format | eloflexsections | moodlecourse |
    When I log in as "manager1"
    And I am on site homepage
    When I press "Add a new course"
    And I set the following fields to these values:
      | Course full name  | My first course          |
      | Course short name | myfirstcourse            |
    And the following fields match these values:
      | Format            | Elo flexible sections format |
    And I set the field "Number of sections" to "5"
    And I press "Save and display"
    And I should see "Topic 1"
    And I should see "Topic 5"
    And I should not see "Topic 6"

  Scenario: Changing course format from topics to eloflexsections
    Given the following "courses" exist:
      | fullname | shortname | format | numsections |
      | Course 1 | C1        | topics | 3           |
    When I log in as "manager1"
    And I am on "Course 1" course homepage
    And I select "Settings" from secondary navigation
    And I set the following fields to these values:
      | Format            | Elo flexible sections format |
    And I wait to be redirected
    And I expand all fieldsets
    And I should not see "Number of sections"
    And I press "Save and display"
    And I should see "Topic 1"
    And I should see "Topic 3"
    And I should not see "Topic 4"

  Scenario: Deleting course in eloflexsections format
    And the following "courses" exist:
      | fullname | shortname | format       | numsections |
      | Course 1 | C1        | eloflexsections | 0           |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section | completion |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       | 1          |
    When I log in as "manager1"
    And I am on "Course 1" course homepage
    And I select "Settings" from secondary navigation
    And I press "Save and display"
    And I go to the courses management page
    And I click on "delete" action for "Course 1" in management course listing
    And I press "Delete"
    And I should see "Deleting C1"
    And I should see "C1 has been completely deleted"
    And I press "Continue"
