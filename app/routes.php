<?php
use Mortar\Http\Router;
use Foundation\Tools\Debug;

Router::get('/', function($mortar) {
	Debug::show($mortar);
});

Router::get('/:key/', function($mortar, $arguments) {
	Debug::show($arguments);
});
