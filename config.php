<?php session_start();

define('MORTAR_VERSION', '0.3.0');

define('APP_DIR', CLASS_DIR.'mortar'.DS.'app'.DS);
	define('APP_ROUTES', APP_DIR.'routes.php');
	define('APP_VIEWS', APP_DIR.'views'.DS);
		define('VIEWS_TEMPLATES', APP_VIEWS.'templates'.DS);
		define('VIEWS_COMPILED', APP_VIEWS.'compiled'.DS);