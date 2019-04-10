<?php
use Mortar\Engine\Core;
use Mortar\Foundation\Tools\Debug;

$mortar = Core::getInstance();
$router = $mortar->component('router');

$router->get('/', 'TestController@test');