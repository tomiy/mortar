<?php
use Mortar\Http\Router;
use Foundation\Tools\Debug;

Router::get('/', function() {
	Debug::show('hello world');
});

Router::get('/static/', function() {
	Debug::show('static route');
});

Router::get('/int:key/str:test?/', function($key, $test = 'default') {
	Debug::show("$key/$test");
	Debug::show($_GET);
});
