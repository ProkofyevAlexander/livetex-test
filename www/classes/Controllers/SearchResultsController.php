<?php

namespace LivetexTest\Controllers;


class SearchResultsController extends BaseController
{
    public function all() {
        return $this->getResponse(
            'searchResults/all.twig',
            array()
        );
    }
    public function forSite() {
        return $this->getResponse(
            'searchResults/forSite.twig',
            array()
        );
    }
}