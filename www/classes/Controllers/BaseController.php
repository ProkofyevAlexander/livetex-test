<?php

namespace LivetexTest\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;

use LivetexTest\System\Views;

abstract class BaseController
{

    private static $urlGenerator;

    public function __construct()
    {
        $this->response = new Response();
    }

    public static function setUlrGenerator(UrlGenerator $urlGenerator) {
        self::$urlGenerator = $urlGenerator;
    }

    /**
     * @return UrlGenerator
     */
    private function getUlrGenerator() {
        return self::$urlGenerator;
    }

    protected function getResponse($template, $templateData) {

        $template = Views::getLoader()->loadTemplate($template);
        $html = $template->render($templateData);

        $this->response->setContent($html);

        return $this->response;
    }

    protected function redirect($path) {
        return new RedirectResponse( $this->getUlrGenerator()->generate($path));
    }

    private $response;
}