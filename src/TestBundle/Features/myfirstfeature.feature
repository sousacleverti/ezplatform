Feature: Basic Rui and Luisa and Sousa BDD tests

    @javascript
    Scenario: I want to click stuff
        Given I visit homesweethome
        When I click the "eZ Mountains"
        Then I should see "eZ Mountains"