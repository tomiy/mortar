<?php
use Mortar\Http\Router;
use Foundation\Tools\Debug;

Router::get('/', function() {
	echo 'hello world';
});
Router::get('/int:key/str:test?/', function($key, $test = 'lol') {
	echo "$key/$test";
});
