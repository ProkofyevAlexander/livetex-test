<?php

namespace LivetexTest\Controllers;

class MainController extends BaseAbstractController
{
    public function index()
    {
        return $this->getResponse(
            'index.twig',
            array()
        );
    }

    public function param($test) {

        return $this->getResponse(
            'index.twig',
            array(
                'param' => $test
            )
        );
    }
}