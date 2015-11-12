<?php

namespace CustomSearchBundle\Controller;

use CustomSearchBundle\Entity\Search;
use CustomSearchBundle\Form\SearchType;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller {
/*
    public function indexAction($name) {
        $repository = $this->getRepository();
        $content = $repository->sudo(
                function () use ($repository, $name) {
            $contentTypeService = $repository->getContentTypeService();
            $contentType = $contentTypeService->loadContentTypeByIdentifier('folder');
            $locationService = $repository->getLocationService();
            $locationCreateStruct = $locationService->newLocationCreateStruct(2);

            $contentService = $repository->getContentService();
            $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
            $contentCreateStruct->setField('name', $name);
            $contentDraft = $contentService->createContent($contentCreateStruct, array($locationCreateStruct));

            return $content = $contentService->publishVersion($contentDraft->versionInfo);
        }
        );
        return $this->render(
                        'TrainingLuisaSousaBundle:Default:index.html.twig', array(
                    'name' => $name,
                    'content' => $content
                        )
        );
    }
    public function searchAction($id) {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();

        $query = new Query();
        $criterion = new Criterion\ParentLocationId($id);
        $query->query = $criterion;

        $searchResult = $searchService->findContent($query);
        $searchHits = $searchResult->searchHits;
        $totalHits = $searchResult->totalCount;
        $contents = array();
        foreach ($searchHits as $searchHit) {
            $contents[] = $searchHit->valueObject->getVersionInfo()->getName();
        }

        return $this->render(
                        'TrainingLuisaSousaBundle:Default:search.html.twig', array(
                    'count' => $totalHits,
                    'contents' => $contents
                        )
        );
    }
 */

    public function searchAction(Request $request) {
        $availableContentTypes = $this->getAvailableContentTypes();
        $search = new Search($availableContentTypes);

        $form = $this->createForm(new SearchType(), $search, array(
            'method' => 'POST')
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

    /* Show content in a custom template in order to be able to add some custom javascript */
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

    /* Return an array with all available ezpublish content type indentifiers and names */
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
