<?php

namespace LivetexTest\Controllers;

class MainController extends BaseController
{
    public function index()
    {
        return $this->getResponse(
            'main/index.twig',
            array()
        );
    }
}