<?php
use Mortar\Engine\Core;
use Mortar\Foundation\Tools\Debug;

$mortar = Core::getInstance();
$router = $mortar->component('router');

$router->get('/', function() use($mortar) {
    Debug::show('hello world');
    $mortar->view('testtemplate');
});

$router->get('/routes/', function() use($router) {
    Debug::show($router->routes());
});

$router->group('/controller/', function($routerGroup) {
    $routerGroup->get('/', 'TestController@test');
    $routerGroup->get('/int:key/', 'TestController@key');

    $routerGroup->group('/test/', function($secondGroup) {
        $secondGroup->get('/int:key/', 'TestController@key');
    }, function() {
        echo 'abc ';
    });
}, 'TestMiddleware');

$router->get('/int:key/str:test?/', function($key, $test = 'default') {
    Debug::show("$key/$test");
    Debug::show($_GET);
});

$router->get('/middleware/', function() {
    echo '2nd';
}, 'TestMiddleware');
