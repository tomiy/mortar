<?php
namespace Mortar\Http;

use Mortar\Mortar;

abstract class Router {
	/**
	 * The routes + attached middleware
	 * @var array
	 */
	private static $routes = [];
	/**
	 * The current group prefix + attached middleware
	 * @var array
	 */
	private static $group;
	/**
	 * The 404 callback
	 * @var callback
	 */
	private static $notfound;

	/**
	 * The allowed methods (used to check for forced method via $_POST)
	 * @var array
	 */
	private static $methods = ['GET', 'POST', 'PUT', 'DELETE'];
	/**
	 * The shortcuts used to parse to regex (makes route writing much easier)
	 * @var array
	 */
	private static $shorthands = [
		'int' => '\d',
		'str' => '[a-zA-Z-]',
		'all' => '[\w-]'
	];

	/**
	 * Includes the routes file
	 */
	public static function routes() {
		require_once CLASS_DIR.'app/routes.php';
	}

	/**
	 * Sets the 404 callback
	 * @var callback
	 */
	private static notFound($callback) {
		static::$notfound = $callback;
	}

	/**
	 * Shorthand function for get requests
	 * @param string $route    the route we want to match
	 * @param mixed  $callback the callback called when the route is matched
	 * @param mixed  $before   the middleware called before if the route is matched
	 */
	public static function get($route, $callback, $before = null) {
		static::addRoute('GET', $route, $callback, $before);
	}
	/**
	 * Shorthand function for post requests
	 * @param string $route    the route we want to match
	 * @param mixed  $callback the callback called when the route is matched
	 * @param mixed  $before   the middleware called before if the route is matched
	 */
	public static function post($route, $callback, $before = null) {
		static::addRoute('POST', $route, $callback, $before);
	}
	/**
	 * Shorthand function for put requests
	 * @param string $route    the route we want to match
	 * @param mixed  $callback the callback called when the route is matched
	 * @param mixed  $before   the middleware called before if the route is matched
	 */
	public static function put($route, $callback, $before = null) {
		static::addRoute('PUT', $route, $callback, $before);
	}
	/**
	 * Shorthand function for delete requests
	 * @param string $route    the route we want to match
	 * @param mixed  $callback the callback called when the route is matched
	 * @param mixed  $before   the middleware called before if the route is matched
	 */
	public static function delete($route, $callback, $before = null) {
		static::addRoute('DELETE', $route, $callback, $before);
	}

	/**
	 * Adds a parsed route to the collection with its callback and middleware(s)
	 * @param string $method   the method that should match this route
	 * @param string $route    the route we want to match
	 * @param mixed  $callback the callback called when the route is matched
	 * @param mixed  $before   the middleware called before if the route is matched
	 */
	private static function addRoute($method, $route, $callback, $before) {
		// force slashes on both sides
		$route = '/'.trim($route,'/').'/';

		// prepend group route if we can
		if(static::$group != null) {
			$route = rtrim(static::$group['route'], '/').$route;
		}

		// if dynamic route, parse, else just make it regex friendly
		if(strpos($route, ':')) {
			$route = static::parseRoute($route);
		} else $route = str_replace('/', '\/', $route);

		// check for controller methods
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

		// check for middleware methods
		if(!is_callable($before)) {
			if(method_exists($before, 'handle')) {
				$before = [
					'class' => $before,
					'function' => 'handle'
				];
			}
		}

		// add route to collection
		static::$routes[$method][$route] = [
			'callback' => $callback,
			'before' => [
				$before
			]
		];

		// add group middleware if we can
		if(static::$group['before'] != null) {
			array_unshift(static::$routes[$method][$route]['before'], static::$group['before']);
		}
	}

	/**
	 * Set a group then walk through a callback of routes to apply it
	 * @param string   $route    the route prefix
	 * @param callback $callback the callback of routes
	 * @param mixed    $before   the group middleware
	 */
	private static function group($route, $callback, $before = null) {
		static::$group = [
			'route' => $route,
			'before' => $before
		];
		$callback();
		static::$group = null;
	}

	/**
	 * Parses the route into a nice, matchable regex
	 * @param  string $route the route we want to parse
	 * @return string        the parsed route
	 */
	private static function parseRoute($route) {
		// check if we're at top level (recursion has slashes trimmed)
		$parsedRoute = $route[0] == '/'?'\/':'';
		// get route parts (array_filter to trim blank values)
		$aRoute = $remainingRoute = array_filter(explode('/', $route));
		foreach($aRoute as $routePart) {
			// if optional part
			if(strpos($routePart, '?')) {
				// go into recursion, embed the rest of the route into an optional regex
				$remainingRoute[0] = str_replace('?', '', $routePart);
				$parsedRoute .= '('.static::parseRoute(implode('/', $remainingRoute)).')?';
				break;
			}
			// if the route part is dynamic
			if(strpos($routePart, ':')) {
				list($pattern, $name) = explode(':', $routePart);
				// replace the shorthands with regex
				$pattern = str_replace(
					array_keys(static::$shorthands),
					array_values(static::$shorthands),
					empty($pattern)?'all':$pattern);
				// add a nice matchable pattern to the parsed route
				$parsedRoute .= "(?P<$name>$pattern+)\/";
			} else $parsedRoute .= $routePart.'\/'; // else just add the part
			// remove the processed route part from the remaining route
			array_shift($remainingRoute);
		}
		return $parsedRoute;
	}

	/**
	 * try to match the uri to a route, and launch callbacks as applicable
	 */
	public static function dispatch() {
		$found = false;
		// get uri and method
		$uri = explode('?', str_replace(dirname($_SERVER['PHP_SELF']), '', $_SERVER['REQUEST_URI']))[0];
		$method = (isset($_POST['_method']) && in_array(strtoupper($_POST['_method']), static::$methods))
			?strtoupper($_POST['_method']):strtoupper($_SERVER['REQUEST_METHOD']);

		//TODO: csrf protection here
		// <input type="hidden" name="token" value="<?= hash_hmac('sha256', $uri, $_SESSION['csrf_token']); ? >" />

		// $calc = hash_hmac('sha256', '$uri, $_SESSION['csrf_token']);
		// if (hash_equals($calc, $_POST['token'])) {
		//     // Continue...
		// }

		// if static method, callback and bail out
		if(in_array(str_replace('/', '\/', $uri), static::$routes[$method])) {
			$found = true;
			foreach (static::$routes[$method]['before'] as $middleware) {
				static::call($middleware);
			}
			static::call($routes[$method]['callback']);

		// else try looping through the table and match a regex
		} else foreach (static::$routes[$method] as $route => $callbacks) {
			$callback = $callbacks['callback'];
			$before = $callbacks['before'];
			// if match then callback and bail out
			if(preg_match("/^$route$/", $uri, $arguments)) {
				$found = true;
				// remove non custom matches
				$arguments = array_filter($arguments, function($key) {
					return !is_numeric($key);
				}, '2');

				foreach ($before as $middleware) {
					static::call($middleware);
				}

				static::call($callback);
				break;
			}
		}

		// if not found display 404
		if(!$found) {
			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
			if(is_callable(static::$notfound)) $notfound();
			else echo '404 Not Found';
			exit;
		}
	}

	private static call($callback, $arguments = null) {
		if(is_callable($callback)) $callback(...$arguments);
		else if(is_array($callback)) {
			call_user_func_array([$callback['class'], $callback['function']], $arguments);
		}
	}
}
