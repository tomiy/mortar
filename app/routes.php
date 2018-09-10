<?php
use Mortar\Http\Router;
use Foundation\Tools\Debug;

$router = new Router();

$router->get('/', function() {
	Debug::show('hello world');
});

$router->group('/controller/', function($routerGroup) {
	$routerGroup->get('/', 'App\Controller\TestController@test');
	$routerGroup->get('/int:key/', 'App\Controller\TestController@key');

	$routerGroup->group('/test/', function($secondGroup) {
		$secondGroup->get('/int:key/', 'App\Controller\TestController@key');
	});
}, 'App\Middleware\TestMiddleware');

$router->get('/int:key/str:test?/', function($key, $test = 'default') {
	Debug::show("$key/$test");
	Debug::show($_GET);
});

$router->get('/middleware/', function() {
	echo '2nd';
}, 'App\Middleware\TestMiddleware');
