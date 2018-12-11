<?php require_once '../setup.php';

use Mortar\Mortar\Core;
use Mortar\Mortar\Http\Request;
use Mortar\Mortar\Http\Router;

$mortar = Core::getInstance([
    new Request($_GET, $_POST, $_SESSION, $_COOKIE, $_SERVER)
]);

require_once APP_ROUTES;

Router::dispatch();

$mortar->display(true);
