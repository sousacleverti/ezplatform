<?php

namespace CustomSearchBundle\Entity;
/**
 * Description of ContentType
 *
 * @author sousa
 */
class ContentType {

    protected $name;

    function getName() {
        return $this->name;
    }

    function setName($name) {
        $this->name = $name;
    }
}
