<?php require_once '../setup.php';

use Mortar\Mortar\Mortar;
use Mortar\Mortar\Http\Request;
use Mortar\Mortar\Http\Router;

Router::loadRequest(new Request($_GET, $_POST, $_SESSION, $_COOKIE, $_SERVER));

$mortar = Mortar::getInstance();

require_once APP_ROUTES;

Router::dispatch();

$mortar->display(true);
