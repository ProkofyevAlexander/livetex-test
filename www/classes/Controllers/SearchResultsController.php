<?php

namespace LivetexTest\Controllers;


class SearchResultsController extends BaseAbstractController
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