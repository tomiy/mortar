<?php define('DS', DIRECTORY_SEPARATOR);

/**
 * Gets the relative path between two paths
 * @param  string $source the source path
 * @param  string $destin the destination path
 * @return string         the relative path
 */
function relativePath($source, $destin) {
    $arFrom = explode(DS, rtrim($source, DS));
    $arTo = explode(DS, rtrim($destin, DS));
    while(count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0])) {
        array_shift($arFrom);
        array_shift($arTo);
    }
    return rtrim(str_pad('', count($arFrom) * 3, '..'.DS).implode(DS, $arTo), DS);
}

function refresh_token() {
    $_SESSION['csrf_token'] = null;
    generate_token();
}

function generate_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

define('MORTAR_VERSION', '0.3.1');
define('CLASS_DIR', relativePath(getcwd(), dirname(__DIR__)).DS);
define('CURRENT_URI', explode('?', str_replace(dirname($_SERVER['PHP_SELF']), '', $_SERVER['REQUEST_URI']))[0]);

set_include_path(get_include_path().PATH_SEPARATOR.CLASS_DIR);
spl_autoload_extensions('.class.php');
spl_autoload_register();

session_start();
generate_token();

require_once __DIR__.DS.'config.php';
