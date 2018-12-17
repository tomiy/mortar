<?php
use Mortar\Mortar\Core;
use Mortar\Mortar\Http\Router;
use Mortar\Foundation\Tools\Debug;

$router = Router::getInstance([
    Core::getInstance()
]);

$router->get('/', function() {
    Debug::show('hello world');
});

$router->get('/routes/', function() use($router) {
    Debug::show($router->routes());
});

$router->group('/controller/', function($routerGroup) {
    $routerGroup->get('/', 'Mortar\App\Controllers\TestController@test');
    $routerGroup->get('/int:key/', 'Mortar\App\Controllers\TestController@key');

    $routerGroup->group('/test/', function($secondGroup) {
        $secondGroup->get('/int:key/', 'Mortar\App\Controllers\TestController@key');
    }, function() {
        echo 'abc ';
    });
}, 'Mortar\App\Middlewares\TestMiddleware');

$router->get('/int:key/str:test?/', function($key, $test = 'default') {
    Debug::show("$key/$test");
    Debug::show($_GET);
});

$router->get('/middleware/', function() {
    echo '2nd';
}, 'Mortar\App\Middlewares\TestMiddleware');
