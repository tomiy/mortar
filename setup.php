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

function escape($string) {
	return htmlentities(mb_convert_encoding($string, 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8');
}

function stop() { exit; }

define('CLASS_DIR', relativePath(getcwd(), dirname(__DIR__)).DS);
set_include_path(get_include_path().PATH_SEPARATOR.CLASS_DIR);
spl_autoload_extensions('.class.php');
spl_autoload_register();

require_once __DIR__.DS.'config.php';
