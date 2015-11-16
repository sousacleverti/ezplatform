<?php

namespace CustomSearchBundle\Controller;

use CustomSearchBundle\Entity\Search;
use CustomSearchBundle\Form\SearchType;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller {
    /**
     * Action for the search form and search form submition
     * 
     * @param Request $request
     * @return Response A Response instance
     */
    public function searchAction(Request $request) {
        $availableContentTypes = $this->getAvailableContentTypes();
        $search = new Search($availableContentTypes);

        // GET request method to enable sharing the result search link
        $form = $this->createForm(new SearchType(), $search, array(
            'method' => 'GET')
        );
        $form->handleRequest($request);
        // Render search results when the form is successfully submited
        if ($form->isValid()) {
            $searchQuery = $search->getSearchQuery();
            $contentTypes = $search->getContentTypes();
            $searchResults = $this->searchFor($searchQuery, $contentTypes);
            return $this->render('CustomSearchBundle:Search:searchResults.html.twig', array(
                        'search_query' => $searchQuery,
                        'contentTypes' => $availableContentTypes,
                        'contents' => $searchResults)
            );
        }
        // Render the search form
        return $this->render('CustomSearchBundle:Search:index.html.twig', array(
                    'form' => $form->createView())
        );
    }

    /**
     * Show content in a custom template in order to be able to add some custom javascript
     *
     * @param type $locationId
     * @param type $contentId
     * @param type $custom_search
     * @return Response A Response instance
     */
    public function contentAction($locationId, $contentId, $custom_search) {
        return $this->render('CustomSearchBundle:Search:hilightingTemplate.html.twig', array(
                    'locationId' => $locationId,
                    'contentId' => $contentId,
                    'custom_search' => $custom_search)
        );
    }

    /***********************************************************************************\
     *                  START of eZ Publish Public API functions                       *
    \***********************************************************************************/

    /**
     * Return an array with all available ezpublish content type indentifiers and names
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType[]
     *
     */
    private function getAvailableContentTypes() {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();
        // Get all available ezpublish content type groups
        $contentTypeGroups = $contentTypeService->loadContentTypeGroups();
        $contentTypes = array();
        // iterate over all $contentTypeGroups
        foreach ($contentTypeGroups as $ctg_it) {
            // iterate over all contentTypes from $contentTypeGroups
            $contentTypesFromGroup = $contentTypeService->loadContentTypes($ctg_it);
            foreach ($contentTypesFromGroup as $ctfg_it) {
                // build a content type array, filled with: [indentifier => Name]
                $contentTypes[$ctfg_it->id] = $ctfg_it->getName('eng-GB');
            }
        }
        return $contentTypes;
    }

    /* Return an array with all search results */
    private function searchFor($searchQuery, $searchAtContentTypes) {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();

        $criterion1 = new Criterion\FullText($searchQuery);
        $criterion2 = new Criterion\ContentTypeId($searchAtContentTypes);

        $query = new Query();
        $query->query = new Criterion\LogicalAnd(array(
            $criterion1,
            new Criterion\LogicalOr(array($criterion2))
        ));

        $searchResult = $searchService->findContent($query);
        $searchHits = $searchResult->searchHits;
        $totalHits = $searchResult->totalCount;
        $contents = array();
        foreach ($searchHits as $searchHit)
            $contents[] = $searchHit->valueObject;

        return $contents;
    }

    /***********************************************************************************\
     *                   END of eZ Publish Public API functions                        *
    \***********************************************************************************/
}
