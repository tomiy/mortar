<?php require_once '../foundation/autoloader.php';

use Mortar\Mortar;
use Mortar\Http\Router;

$mortar = Mortar::getInstance();

Mortar::routes();
Router::dispatch();

$mortar->display(true);
