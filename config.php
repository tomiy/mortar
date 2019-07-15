<?php

define('APP_DIR', 'app'.DS);
    define('APP_PARSER', APP_DIR.'parser.php');
    define('APP_ROUTES', APP_DIR.'routes.php');
    define('APP_VIEWS', APP_DIR.'views'.DS);
        define('VIEWS_TEMPLATES', APP_VIEWS.'templates'.DS);
        define('VIEWS_COMPILED', APP_VIEWS.'compiled'.DS);
        define('VIEWS_EXTENSION', '.tpl');

define('PARSER_OPEN', '{{');
define('PARSER_STOP', '}}');
define('PARSER_MASK', count_chars(PARSER_OPEN.PARSER_STOP, 3));

define('DB_LINK', '<connection string>');
define('DB_USER', '<user string>');
define('DB_PASS', '<password string>');
define('NODB', false);