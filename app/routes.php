<?php
use Mortar\Mortar\Core;
use Mortar\Mortar\Http\Router;
use Mortar\Foundation\Tools\Debug;

$router = new Router(Mortar::getInstance());

$router->get('/', function() {
    Debug::show('hello world');
});

$router->group('/controller/', function($routerGroup) {
    $routerGroup->get('/', 'Mortar\App\Controllers\TestController@test');
    $routerGroup->get('/int:key/', 'Mortar\App\Controllers\TestController@key');

    $routerGroup->group('/test/', function($secondGroup) {
        $secondGroup->get('/int:key/', 'Mortar\App\Controllers\TestController@key');
    });
}, 'Mortar\App\Middlewares\TestMiddleware');

$router->get('/int:key/str:test?/', function($key, $test = 'default') {
    Debug::show("$key/$test");
    Debug::show($_GET);
});

$router->get('/middleware/', function() {
    echo '2nd';
}, 'Mortar\App\Middlewares\TestMiddleware');
