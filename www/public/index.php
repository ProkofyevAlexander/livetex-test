<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

use LivetexTest\System\Templates;

define('DEBUG', true);

$configDir = __DIR__ . '/../config';
$templatesDir = __DIR__ . '/../templates';
$cacheTwigDir = __DIR__ . '/../cache/twig';

/* Templates */
$loader = new Twig_Loader_Filesystem($templatesDir);
$twig = new Twig_Environment($loader, array(
    'debug' => DEBUG,
    'cache' => $cacheTwigDir
));

Templates::setLoader($twig);

/* Routing */
$locator = new FileLocator(array($configDir));
$loader = new YamlFileLoader($locator);
$routes = $loader->load('routes.yml');
$context = new RequestContext('/');
$matcher = new UrlMatcher($routes, $context);

/* Request handling, response */
$request = Request::createFromGlobals();
$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));

$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

$kernel = new HttpKernel($dispatcher, $controllerResolver, new RequestStack(), $argumentResolver);

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);