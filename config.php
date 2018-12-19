<?php
define('CURRENT_URI', explode('?', str_replace(dirname($_SERVER['PHP_SELF']), '', $_SERVER['REQUEST_URI']))[0]);

define('MORTAR_VERSION', '0.3.0');

define('APP_DIR', CLASS_DIR.'mortar'.DS.'app'.DS);
    define('APP_PARSER', APP_DIR.'parser.php');
    define('APP_ROUTES', APP_DIR.'routes.php');
    define('APP_VIEWS', APP_DIR.'views'.DS);
        define('VIEWS_TEMPLATES', APP_VIEWS.'templates'.DS);
        define('VIEWS_COMPILED', APP_VIEWS.'compiled'.DS);
        define('VIEWS_EXTENSION', '.tpl');

define('PARSER_OPEN', '{{m:');
define('PARSER_STOP', '}}');
define('PARSER_MASK', count_chars(PARSER_OPEN.PARSER_STOP, 3));
