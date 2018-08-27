<?php require_once '../foundation/autoloader.php';

use Mortar\Mortar;

$mortar = Mortar::getInstance();

include_once $mortar->routes();
