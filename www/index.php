<?php require_once '../setup.php';

use Mortar\Mortar\Mortar;
use Mortar\Mortar\Http\Router;

$mortar = Mortar::getInstance();

require_once APP_ROUTES;

Router::dispatch();

$mortar->display(true);