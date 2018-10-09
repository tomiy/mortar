<?php define('DS', DIRECTORY_SEPARATOR);
/**
 * Gets the relative path between two paths
 * @param  string $from the source path
 * @param  string $to   the destination path
 * @return string       the relative path
 */
function relativePath($from, $to) {
	$arFrom = explode(DS, rtrim($from, DS));
	$arTo = explode(DS, rtrim($to, DS));
	while(count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0])) {
		array_shift($arFrom);
		array_shift($arTo);
	}
	return str_pad('', count($arFrom) * 3, '..'.DS).implode(DS, $arTo);
}

define('CLASS_DIR', relativePath(getcwd(), dirname(__DIR__)).DS);
set_include_path(get_include_path().PATH_SEPARATOR.CLASS_DIR);
spl_autoload_extensions('.class.php');
spl_autoload_register();

require_once __DIR__.DS.'config.php';
require_once __DIR__.DS.'functions.php';
