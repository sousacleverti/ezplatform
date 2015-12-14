# https://github.com/miguelcleverti/PlatformUIBundle/blob/codeResctructure/Features/Context/SubContext/CommonActions.php

Feature: First BDD PlatformUI Tests

    @javascript
    Scenario: I want to click everywhere
        Given I go to PlatformUI app with username "admin" and password "publish"
        Given I click on the navigation zone "Content"
        Given I click on the navigation item "Content structure"
        Given I click on the discovery bar button "Content tree"
        Given I click on the action bar button "Create"

    @javascript
    Scenario: [tc-2768:EZP-24973] Non-containers are being allowed as locations
        Given I go to PlatformUI app with username "admin" and password "publish"
        And I click on the navigation zone "Admin Panel"
        And I click on the navigation item "Content types"
        And I click on the content type "Content" link
        And I click on the button "Create a content type"
        When I fill in "Name" with "NonContainerTest"
        And I fill in "Identifier" with "nc_test"
        And I uncheck "Container" checkbox
        And I add a field type "Text line" with:
                | Field      | Value   |
                | Identifier | nc_name |
                | Name       | Name    |
        And I click at the "OK" button
        And I click on the navigation zone "Content"
        And I click on the navigation item "Content structure"
        And I click on the action bar button "Create"
        And I click on the content type "NonContainerTest"
        When I fill in "Name" with "Test1"
        And I click on the button "Publish"
        And I click on the navigation zone "Content"
        And I click on the navigation item "Content structure"
        And I click on the action bar button "Create"
        And I click on the content type "Folder"
        When I fill in "Name" with "Folder1"
        And I click on the button "Publish"
        And I click on the "Locations" link
        And I click on the button "Add location"
        When I select the "Home/Test1" folder in the Universal Discovery Widget
        Then I can't click the button "Choose this content"

    @runnable
    @javascript
    Scenario: [tc-2768:EZP-24973] Non-containers are being allowed as locations
        Given I go to PlatformUI app with username "admin" and password "publish"
        And I have a Content Type with identifier "NonContainerTest2" in Group with identifier "Content" with fields:
                | identifier | type       | name |
                | title      | ezstring   | Name |
        And I create a content of content type "NonContainerTest" with:
                | Field      | Value   |
                | Identifier | nc_name |
                | Name       | Name    |
