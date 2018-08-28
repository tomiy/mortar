<?php require_once '../foundation/autoloader.php';

use Mortar\Mortar;
use Mortar\Http\Router;

$mortar = Mortar::getInstance();

include_once $mortar->routes();

Router::dispatch();
