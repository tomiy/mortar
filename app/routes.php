<?php
use Mortar\Foundation\Tools\DependencyInjector as DI;

$mortar = DI::get('core');
$router = DI::get('router');


$router->group('/', function($r) {
    $r->get('/', 'TestController@test');

}, 'CsrfMiddleware');