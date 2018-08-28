<?php
use Mortar\Http\Router;

Router::get('/', function($mortar) {
	$mortar->debug('👍');
});

Router::get('/:key/', function($mortar, $arguments) {
	$mortar->debug($arguments);
});
