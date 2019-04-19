<?php
use Mortar\Foundation\DependencyInjector;

$container = DependencyInjector::getInstance();

$mortar = $container->get('core');
$router = $container->get('router');

$router->get('/', 'TestController@test');