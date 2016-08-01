<?php

namespace LivetexTest\Controllers;

use Symfony\Component\HttpFoundation\Response;

use LivetexTest\System\Views;

abstract class BaseAbstractController
{

    public function __construct()
    {
        $this->response = new Response();
    }

    public function getResponse($template, $templateData) {

        $template = Views::getLoader()->loadTemplate($template);
        $html = $template->render($templateData);

        $this->response->setContent($html);

        return $this->response;
    }

    private $response;
}