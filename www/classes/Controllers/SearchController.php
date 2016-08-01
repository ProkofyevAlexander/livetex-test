<?php

namespace LivetexTest\Controllers;

use LivetexTest\Services\Search;

class SearchController extends BaseController
{
    public function newSearch()
    {
        $url = isset($_POST['url']) ? $_POST['url'] : '';
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $text = isset($_POST['text']) ? $_POST['text'] : '';

        if (!$this->validateForm($url, $type, $text)) {
            return $this->redirect('main-index');
        }

        $search = new Search();
        $search->makeSearch($url, $type, $text);

        return $this->getResponse('results-temp.twig', array(
            'results' => $search->makeSearch($url, $type, $text)
        ));
        // return $this->redirect('searchResults-all');
    }

    private function validateForm($url, $type, $text)
    {
        $urlIsValid = $url && preg_match('/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $url);
        $typeIsValid = in_array($type, array(
            Search::TYPE_IMAGE,
            Search::TYPE_LINK,
            Search::TYPE_TEXT
        ));
        $textIsValid = $type !== 'text' || $text !== '';

        return $urlIsValid && $typeIsValid && $textIsValid;
    }
}