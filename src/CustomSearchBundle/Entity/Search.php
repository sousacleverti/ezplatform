<?php

namespace CustomSearchBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author sousa
 */
class Search {
    protected $searchQuery;
    protected $contentTypes;

    public function __construct($contentTypes) {
        $this->setContentTypes($contentTypes);
    }

    function getSearchQuery() {
        return $this->searchQuery;
    }

    function getContentTypes() {
        return $this->contentTypes;
    }

    function setSearchQuery($searchQuery) {
        $this->searchQuery = $searchQuery;
    }

    function setContentTypes($contentTypes) {
        $this->contentTypes = $contentTypes;
    }
}
