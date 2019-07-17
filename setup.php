<?php

define('DS', DIRECTORY_SEPARATOR);
define('MORTAR_VERSION', '0.3.1');
define('CLASS_DIR', relativePath(getcwd(), dirname(__DIR__)) . DS);
define(
    'CURRENT_URI',
    str_pad( // edge case if we're at site root (REQUEST_URI & PHP_SELF are both '/')
        explode( // remove the get parameters
            '?',
            str_replace( // in case we're in a subdirectory, remove the root path from the uri
                dirname($_SERVER['PHP_SELF']),
                '',
                $_SERVER['REQUEST_URI']
            )
        )[0],
        1,
        '/'
    )
);

/**
 * Gets the relative path between two paths
 * @param  string $source the source path
 * @param  string $destin the destination path
 * @return string         the relative path
 */
function relativePath($source, $destin)
{
    $arFrom = explode(DS, rtrim($source, DS));
    $arTo = explode(DS, rtrim($destin, DS));
    while (count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0])) {
        array_shift($arFrom);
        array_shift($arTo);
    }
    return rtrim(str_pad('', count($arFrom) * 3, '..' . DS) . implode(DS, $arTo), DS);
}

function path($path = null)
{
    static $o;
    if ($path) $o = $path;
    return $o ? $o : CLASS_DIR . 'mortar' . DS;
}

set_include_path(get_include_path() . PATH_SEPARATOR . CLASS_DIR);
spl_autoload_extensions('.class.php');
spl_autoload_register();

session_start();

require_once __DIR__ . DS . 'config.php';
