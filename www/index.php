<?php require_once '../setup.php';

use Mortar\Foundation\Tools\DependencyInjector as DI;

use Mortar\Engine\Core;

use Mortar\Engine\Http\Request;
use Mortar\Engine\Http\Router;
use Mortar\Engine\Http\RouteWorker;
use Mortar\Engine\Http\RouteResponse;

use Mortar\Engine\Build\Parser;
use Mortar\Engine\Build\Database;

//dependency injection

DI::set('request', function() {
    return new Request(
        $_GET,
        $_POST,
        $_SESSION,
        $_COOKIE,
        $_SERVER
    );
});

DI::set('core', function() {
    return new Core(
        DI::get('request'),
        DI::get('router'),
        DI::get('parser'),
        DI::get('database')
    );
});

DI::set('routeworker', function() {
    return new RouteWorker();
});

DI::set('routeresponse', function() {
    return new RouteResponse(DI::get('request'));
});

DI::set('router', function() {
    return new Router(
        DI::get('routeworker'),
        DI::get('routeresponse')
    );
});

DI::set('parser', function() {
    return new Parser();
});

DI::set('database', function() {
    return new Database(DI::get('pdo'));
});

DI::set('pdo', function() {
    return new \PDO(DB_LINK, DB_USER, DB_PASS, [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_EMULATE_PREPARES => false
    ]);
});

//display
$mortar = DI::get('core');
$router = DI::get('router');

require_once path().APP_PARSER;
require_once path().APP_ROUTES;

$router->dispatch();

$mortar->display(true);