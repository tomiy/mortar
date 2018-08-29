<?php
namespace Mortar\Http;

use Mortar\Mortar;

	abstract class Router {
	private static $get = [];
	private static $post = [];
	private static $put = [];
	private static $delete = [];

	public function routes() {
		require_once CLASS_DIR.'app/routes.php';
	}

	public static function get($route, $callback) {
		static::$get[$route] = $callback;
	}

	public static function post($route, $callback) {
		static::$post[$route] = $callback;
	}

	public static function put($route, $callback) {
		static::$put[$route] = $callback;
	}

	public static function delete($route, $callback) {
		static::$delete[$route] = $callback;
	}

	public static function dispatch() {
		$route = str_replace(dirname($_SERVER['PHP_SELF']), '', $_SERVER['REQUEST_URI']);

		switch(strtoupper($_SERVER['REQUEST_METHOD'])) {
			case 'GET': static::check(static::$get, $route);
			break;

			case 'POST': static::check(static::$post, $route);
			break;

			case 'PUT': static::check(static::$put, $route);
			break;

			case 'DELETE': static::check(static::$delete, $route);
			break;

			default: http_response_code(405);
				break;
		}
	}

	private static function check($array, $route) {
		//simple route, callback and bail out
		if(isset($array[$route])) {
			$array[$route](Mortar::getInstance());
			return true;
		} else {
			$arRoute = explode('/', $route);
			foreach ($array as $regexRoute => $callback) {
				$arguments = [];
				$found = true;
				foreach(explode('/', $regexRoute) as $index => $regexRoutePart) {
					//assign argument
					if(preg_match('/:(?P<name>.+)/', $regexRoutePart, $m)) {
						$arguments[$m['name']] = $arRoute[$index];
						continue;
					}
					//mismatch, stop checking
					if(!isset($arRoute[$index]) || $arRoute[$index] != $regexRoutePart) {
						$found = false;
						break;
					}
				}
				if($found) {
					$callback(Mortar::getInstance(), $arguments);
					return true;
				}
			}
			//custom 404
			http_response_code(404);
			return false;
		}
	}
}
