<?php session_start();
function relativePath($from, $to) {
  $arFrom = explode(DIRECTORY_SEPARATOR, rtrim($from, DIRECTORY_SEPARATOR));
  $arTo = explode(DIRECTORY_SEPARATOR, rtrim($to, DIRECTORY_SEPARATOR));
  while(count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0])) {
    array_shift($arFrom);
    array_shift($arTo);
  }
  return str_pad('', count($arFrom) * 3, '..'.DIRECTORY_SEPARATOR).implode(DIRECTORY_SEPARATOR, $arTo);
}

define('CLASS_DIR', relativePath(getcwd(), dirname(__DIR__)).DIRECTORY_SEPARATOR);
set_include_path(get_include_path().PATH_SEPARATOR.CLASS_DIR);
spl_autoload_extensions('.class.php');
spl_autoload_register();
