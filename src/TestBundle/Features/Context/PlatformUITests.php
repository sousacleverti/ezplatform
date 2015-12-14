<?php

namespace TestBundle\Features\Context;

use EzSystems\PlatformUIBundle\Features\Context\PlatformUI;
use EzSystems\BehatBundle\Helper\EzAssertion;
use Behat\Gherkin\Node\TableNode;

class PlatformUITests extends PlatformUI {

    /**
     * @Given I click on the content type :link link
     */
    public function clickLink($link) {
        $this->clickElementByText($link, 'a');
    }

    /**
     * @Given I click on the button :button
     */
    public function clickButton($button) {
        $this->clickElementByText($button, 'button');
    }

    /**
     * @Given I unchecked :label checkbox
     * @When  I uncheck :label checkbox
     *
     * Unchecks the value for the checkbox with name ':label'
     */
    public function uncheckOption($option) {
        $fieldElements = $this->getXpath()->findFields($option);
        EzAssertion::assertElementFound($option, $fieldElements, null, 'checkbox');
        // this is needed for the cases where are checkboxes and radio's
        // side by side, for main option the radio and the extra being the
        // checkboxes values
        if (strtolower($fieldElements[0]->getAttribute('type')) !== 'checkbox') {
            $value = $fieldElements[0]->getAttribute('value');
            $fieldElements = $this->getXpath()->findXpath("//input[@type='checkbox' and @value='$value']");
            EzAssertion::assertElementFound($value, $fieldElements, null, 'checkbox');
        }
        $fieldElements[0]->uncheck();
    }

    /**
     * @Given I add a field type :field with:
     */
    public function iAddFieldType($type, TableNode $fields) {
        $elements = $this->findAllWithWait('.pure-control-group');
        $element = $this->getElementContainsText('Field type selection', '.pure-control-group');
        $select = $element->find('css', 'select');
        $select->selectOption($type);
        $this->clickElementByText('Add field definition', '.ez-button', null, $element);
        $this->waitWhileLoading();
        $internalName = $this->getFieldTypeManager()->getFieldTypeInternalIdentifier($type);
        $formElement = $this->findWithWait(".field-type-$internalName");
        foreach ($fields as $field) {
            $formElement->fillField($field['Field'], $field['Value']);
        }
    }

    /**
     * Finds an HTML element using a css selector and if it contains some text value and returns it.
     *
     * @param string    $text           Text value of the element
     * @param string    $selector       CSS selector of the element
     * @return array
     */
    protected function getElementContainsText($text, $selector) {
        $elements = $this->findAllWithWait($selector);
        foreach ($elements as $element) {
            $elementText = $element->getText();
            if (strpos($elementText, $text) !== false) {
                return $element;
            }
        }
        return false;
    }

    /**
     * @Then I can't click the button :button
     */
    public function iCantClick($button) {
        $element = $this->getElementByText($button, 'button');
        $attr = $element->getAttribute('disabled');
        if ($attr !== 'disabled')
            throw new \Exception("Button '$button' is enabled!");
        return true;
    }

    /**
     * @Given I have a Content Type with identifier :ct_id in Group with identifier :ctg_id with fields:
     */
    public function iCreateContentType($content_identifier, $content_group_identifier, TableNode $fields) {
        $repository = $this->getRepository();
        // Use sudo in order to have permission to create contents
        $repository->sudo(
            function() use($repository, $content_group_identifier, $content_identifier, $fields) {

                $contentTypeService = $repository->getContentTypeService();

                // Create a 'Content' ContentTypeGroup object
                $contentTypeGroup = $contentTypeService->loadContentTypeGroupByIdentifier($content_group_identifier);
                $contentTypeGroups = array($contentTypeGroup);

                // Create the structure of the content type based on the $fields values
                $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct(strtolower($content_identifier));
                $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
                $contentTypeCreateStruct->names = array('eng-GB' => $content_identifier);
                foreach ($fields as $field) {
                    $field = array_change_key_case($field, CASE_LOWER);
                    $fieldDef = $contentTypeService->newFieldDefinitionCreateStruct($field['identifier'], $field['type']);
                    $fieldDef->names = array('eng-GB' => $field['name']);
                    $contentTypeCreateStruct->addFieldDefinition($fieldDef);
                }
                // Create the desired content type
                $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, $contentTypeGroups);
                $contentTypeService->publishContentTypeDraft($contentTypeDraft);
            }
        );
    }

    /**
     * Mink:
     * http://apigen.juzna.cz/doc/Behat/Mink/namespace-Behat.Mink.html
     *
     * PlatformUI:
     * https://github.com/miguelcleverti/PlatformUIBundle/tree/master/Features
     * https://github.com/miguelcleverti/PlatformUIBundle/blob/codeResctructure/Features/Context/SubContext/CommonActions.php
     *
     * Behat eZ Systems:
     * https://github.com/ezsystems/BehatBundle/blob/master/Context/Browser/SubContext/CommonActions.php
     */
}
