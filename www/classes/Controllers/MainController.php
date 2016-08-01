<?php

namespace LivetexTest\Controllers;

class MainController extends BaseAbstractController
{
    public function index()
    {
        return $this->getResponse(
            'main/index.twig',
            array()
        );
    }
}