<?php
namespace Mortar\Http;

use Mortar\Mortar;

	abstract class Router {
	private static $routes = [];

	private static $methods = ['GET', 'POST', 'PUT', 'DELETE'];
	private static $shorthands = [
		'int' => '\d',
		'str' => '[a-zA-Z-]'
	];

	public static function routes() {
		require_once CLASS_DIR.'app/routes.php';
	}

	public static function get($route, $callback, $before = null) {
		return static::addRoute('GET', $route, $callback, $before);
	}

	public static function post($route, $callback, $before = null) {
		return static::addRoute('POST', $route, $callback, $before);
	}

	public static function put($route, $callback, $before = null) {
		return static::addRoute('PUT', $route, $callback, $before);
	}

	public static function delete($route, $callback, $before = null) {
		return static::addRoute('DELETE', $route, $callback, $before);
	}

	private static function addRoute($method, $route, $callback, $before) {
		if(strpos($route, ':')) {
			$route = static::parseRoute($route);
		}

		//TODO: check if callback and before are functions or parse class@function string
		static::$routes[$method][$parsedRoute] = [
			'callback' => $callback,
			'before' => $before
		]
	}

	private static function group($before, $callback) {}

	private static function parseRoute($route) {
		$parsedRoute = $route[0] == '/'?'\/':'';
		$aRoute = $remainingRoute = explode('/', $route);
		foreach($aRoute as $routePart) {
			if(empty($routePart)) {
				array_shift($remainingRoute);
				continue;
			}
			if(strpos($routePart, '?')) {
				$remainingRoute[0] = str_replace('?', '', $routePart);
				$parsedRoute .= '('.static::parseRoute(implode('/', $remainingRoute)).')?';
				break;
			}
			if(preg_match('/(?P<pattern>.*):(?P<name>.+)/', $routePart, $m)) {
				$pattern = str_replace(
					array_keys(static::$shorthands),
					array_values(static::$shorthands),
					empty($m['pattern'])?'str':$m['pattern']);
				$parsedRoute .= "(?P<{$m['name']}>$pattern+)\/";
			} else $parsedRoute .= $routePart.'\/';
			array_shift($remainingRoute);
		}
		return $parsedRoute;
	}

	public static function dispatch() {
		$uri = str_replace(dirname($_SERVER['PHP_SELF']), '', $_SERVER['REQUEST_URI']);
		$method = (isset($_POST['_method']) && in_array(static::$methods, strtoupper($_POST['_method'])))
			?strtoupper($_POST['_method']):strtoupper($_SERVER['REQUEST_METHOD']);

		if(in_array(static::$routes[$method], $uri)) {
			if($before != null) static::$routes[$method]['before']();
			static::$routes[$method]['callback']();
		} else foreach (static::$routes[$method] as $route => list($callback, $before)) {
			if(preg_match($route, $uri, $arguments)) {
				if($before != null) $before();
				call_user_func_array($callback, $arguments);
				break;
			}
		}
	}
}
