<?php require_once '../setup.php';

use Mortar\Foundation\DependencyInjector;
use Mortar\Engine\Core;
use Mortar\Engine\Http\Request;
use Mortar\Engine\Http\Router;
use Mortar\Engine\Http\RouteWorker;
use Mortar\Engine\Http\RouteResponse;

//dependency injection
$container = DependencyInjector::getInstance();

$container->set('request', function($c) {
    return new Request($_GET, $_POST, $_SESSION, $_COOKIE, $_SERVER);
});

$container->set('core', function($c) {
    return Core::getInstance($c->get('request'), $c->get('router'), $c->get('parser'), $c->get('database'));
});

$container->set('routeworker', function($c) {
    return new RouteWorker($c->get('core'), $c->get('router'));
});

$container->set('routeresponse', function($c) {
    return new RouteResponse($c->get('request'));
});

$container->set('router', function($c) {
    return new Router($c->get('core'), $c->get('routeworker'), $c->get('routeresponse'));
});

$container->set('parser', function($c) {
    return new Parser($c->get('core'));
});

$container->set('database', function($c) {
    return new Database($c->get('pdo'));
});

$container->set('pdo', function($c) {
    return new \PDO(DB_LINK, DB_USER, DB_PASS, [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_EMULATE_PREPARES => false
    ]);
});

//display
$mortar = $container->get('core');
$router = $container->get('router');

require_once path().APP_PARSER;
require_once path().APP_ROUTES;

$router->dispatch();

$mortar->display(true);