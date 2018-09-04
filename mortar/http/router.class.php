<?php
namespace Mortar\Http;

use Mortar\Mortar;

abstract class Router {
	private static $routes = [];
	private static $group;

	private static $methods = ['GET', 'POST', 'PUT', 'DELETE'];
	private static $shorthands = [
		'int' => '\d',
		'str' => '[a-zA-Z-]',
		'all' => '[\w-]'
	];

	public static function routes() {
		require_once CLASS_DIR.'app/routes.php';
	}

	public static function get($route, $callback, $before = null) {
		static::addRoute('GET', $route, $callback, $before);
	}

	public static function post($route, $callback, $before = null) {
		static::addRoute('POST', $route, $callback, $before);
	}

	public static function put($route, $callback, $before = null) {
		static::addRoute('PUT', $route, $callback, $before);
	}

	public static function delete($route, $callback, $before = null) {
		static::addRoute('DELETE', $route, $callback, $before);
	}

	private static function addRoute($method, $route, $callback, $before) {
		if(static::$group != null) {
			$route = rtrim(static::$group['route'], '/').'/'.ltrim($route, '/');
		}

		if(strpos($route, ':')) {
			$route = static::parseRoute($route);
		} else $route = str_replace('/', '\/', $route);

		if(!is_callable($callback)) {
			if(strpos($callback, '@')) {
				list($controller, $function) = explode('@', $callback);
				if(method_exists($controller, $function)) {
					$callback = [
						'class' => $controller,
						'function' => $function
					];
				}
			}
		}

		if(!is_callable($before)) {
			if(method_exists($before, 'handle')) {
				$before = [
					'class' => $before,
					'function' => 'handle'
				];
			}
		}

		static::$routes[$method][$route] = [
			'callback' => $callback,
			'before' => [
				$before
			]
		];

		if(static::$group['before'] != null) {
			static::$routes[$method][$route]['before'][] = static::$group['before'];
		}
	}

	private static function group($route, $callback, $before = null) {
		static::$group = [
			'route' => $route,
			'before' => $before
		];
		$callback();
		static::$group = null;
	}

	private static function parseRoute($route) {
		$parsedRoute = $route[0] == '/'?'\/':'';
		$aRoute = $remainingRoute = array_filter(explode('/', $route));
		foreach($aRoute as $routePart) {
			if(strpos($routePart, '?')) {
				$remainingRoute[0] = str_replace('?', '', $routePart);
				$parsedRoute .= '('.static::parseRoute(implode('/', $remainingRoute)).')?';
				break;
			}
			if(preg_match('/(?P<pattern>.*):(?P<name>.+)/', $routePart, $m)) {
				$pattern = str_replace(
					array_keys(static::$shorthands),
					array_values(static::$shorthands),
					empty($m['pattern'])?'all':$m['pattern']);
				$parsedRoute .= "(?P<{$m['name']}>$pattern+)\/";
			} else $parsedRoute .= $routePart.'\/';
			array_shift($remainingRoute);
		}
		return $parsedRoute;
	}

	public static function dispatch() {
		$found = false;
		$uri = explode('?', str_replace(dirname($_SERVER['PHP_SELF']), '', $_SERVER['REQUEST_URI']))[0];
		$method = (isset($_POST['_method']) && in_array(strtoupper($_POST['_method']), static::$methods))
			?strtoupper($_POST['_method']):strtoupper($_SERVER['REQUEST_METHOD']);

		//TODO: csrf protection here
		// <input type="hidden" name="token" value="<?php echo hash_hmac('sha256', $uri, $_SESSION['csrf_token']); ? >" />

		// $calc = hash_hmac('sha256', '$uri, $_SESSION['csrf_token']);
		// if (hash_equals($calc, $_POST['token'])) {
		//     // Continue...
		// }

		if(in_array($uri, static::$routes[$method])) {
			$found = true;
			if(static::$routes[$method]['before'] != null) static::$routes[$method]['before']();
			static::$routes[$method]['callback']();

		} else foreach (static::$routes[$method] as $route => $callbacks) {
			$callback = $callbacks['callback'];
			$before = $callbacks['before'];
			if(preg_match("/^$route$/", $uri, $arguments)) {
				$found = true;
				$arguments = array_filter($arguments, function($key) {
					return !is_numeric($key);
				}, '2');

				foreach ($before as $middleware) {
					if(is_callable($middleware)) $middleware();
					else if(is_array($middleware)) {
						call_user_func([$middleware['class'], $middleware['function']]);
					}
				}

				if(is_callable($callback)) call_user_func_array($callback, $arguments);
				else if(is_array($callback)) {
					call_user_func_array([$callback['class'], $callback['function']], $arguments);
				}
				break;
			}
		}
		//TODO: proper 404
		if(!$found) {
			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
			echo '404';
			exit;
		}
	}
}
