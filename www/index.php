<?php require_once '../setup.php';

use Mortar\Engine\Core;
use Mortar\Engine\Http\Request;
use Mortar\Engine\Http\Router;

$mortar = Core::getInstance([
    new Request($_GET, $_POST, $_SESSION, $_COOKIE, $_SERVER)
]);

require_once path().APP_PARSER;
require_once path().APP_ROUTES;

$mortar->component('router')->dispatch();

$mortar->display(true);
