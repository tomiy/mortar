<?php
use Mortar\Http\Router;
use Foundation\Tools\Debug;

Router::get('/', function() {
	Debug::show('hello world');
});

Router::group('/controller/', function() {
	Router::get('/', 'App\Controller\TestController@test');
	Router::get('/int:key/', 'App\Controller\TestController@key');
}, 'App\Middleware\TestMiddleware');

Router::get('/int:key/str:test?/', function($key, $test = 'default') {
	Debug::show("$key/$test");
	Debug::show($_GET);
});

Router::get('/middleware/', function() {
	echo '2nd';
}, 'App\Middleware\TestMiddleware');
