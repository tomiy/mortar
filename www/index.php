<?php require_once '../foundation/autoloader.php';

use Mortar\Mortar;
use Mortar\Http\Router;

$mortar = Mortar::getInstance();

require_once APP_ROUTES;

Router::dispatch();

$mortar->display(true);
