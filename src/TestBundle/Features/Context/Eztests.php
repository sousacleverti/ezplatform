<?php

namespace TestBundle\Features\Context;

use EzSystems\BehatBundle\Context\Browser\Context;

class Eztests extends Context {
    /**
     * @Given I visit homesweethome
     */
    public function goToHomePage() {
        $this->visit('');
        sleep(5);
    }
    /**
     * @When I click the :ezStuff
     */
    public function clickEZMountain($ezStuff) {
        /**
         *  http://apigen.juzna.cz/doc/Behat/Mink/source-class-Behat.Mink.Session.html#116-124
         */
        $session = $this->getSession();
        $page = $session->getPage();
        $page->clickLink($ezStuff);
        sleep(5);
     }
    /**
     * @Then I should see :ezSomething
     */
    public function seeEZMountains($ezSomething) {
        $page = $this->getSession()->getPage();
        $page->find('xpath', '//label[text()="'. $ezSomething .'"]');
        sleep(5);
    }

    /**
     * http://apigen.juzna.cz/doc/Behat/Mink/namespace-Behat.Mink.html
     */
}