<?php
use Mortar\Mortar\Http\Router;
use Mortar\Foundation\Tools\Debug;

$router = new Router();

$router->get('/', function() {
	Debug::show('hello world');
});

$router->group('/controller/', function($routerGroup) {
	$routerGroup->get('/', 'App\Controllers\TestController@test');
	$routerGroup->get('/int:key/', 'App\Controllers\TestController@key');

	$routerGroup->group('/test/', function($secondGroup) {
		$secondGroup->get('/int:key/', 'App\Controllers\TestController@key');
	});
}, 'App\Middlewares\TestMiddleware');

$router->get('/int:key/str:test?/', function($key, $test = 'default') {
	Debug::show("$key/$test");
	Debug::show($_GET);
});

$router->get('/middleware/', function() {
	echo '2nd';
}, 'App\Middlewares\TestMiddleware');
