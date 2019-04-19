<?php
use Mortar\Foundation\Tools\DependencyInjector as DI;

$mortar = DI::get('core');
$router = DI::get('router');

$router->get('/', 'TestController@test');