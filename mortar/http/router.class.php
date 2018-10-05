<?php
namespace Mortar\Mortar\Http;

use Mortar\Mortar\Mortar;

class Router {
	/**
	 * The routes + attached middleware
	 * @var array
	 */
	private static $routes = [];
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
	 * The method of our request, used to check for csrf
	 * @var string
	 */
	private static $method;

	/**
	* The current group prefix + attached middleware
	* @var array
	*/
	private $group;

	/**
	 * Instanciate a new router
	 * @param string $prefix the route group
	 * @param mixed  $before the group middleware
	 */
	public function __construct($prefix = null, $before = null) {

		if(empty(static::$method)) {
			static::$method = (isset($_POST['_method']) && in_array(strtoupper($_POST['_method']), static::$methods))
				?strtoupper($_POST['_method']):strtoupper($_SERVER['REQUEST_METHOD']);
		}

		if(static::$method != 'GET') {
			$calc = hash_hmac('sha256', CURRENT_URI, $_SESSION['csrf_token']);
			if (!hash_equals($calc, $_POST['_token']) || !in_array(static::$method, static::$methods)) {
				header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
				exit;
			}
		}

		$this->group = [
			'route' => $prefix,
			'before' => $before
		];
	}

	/**
	 * Sets the 404 callback
	 * @var callback
	 */
	private static function notFound($callback) {
		static::$notfound = $callback;
	}

	/**
	 * Shorthand function for get requests
	 * @param string $route    the route we want to match
	 * @param mixed  $callback the callback called when the route is matched
	 * @param mixed  $before   the middleware called before if the route is matched
	 */
	public function get($route, $callback, $before = null) {
		$this->addRoute('GET', $route, $callback, $before);
	}
	/**
	 * Shorthand function for post requests
	 * @param string $route    the route we want to match
	 * @param mixed  $callback the callback called when the route is matched
	 * @param mixed  $before   the middleware called before if the route is matched
	 */
	public function post($route, $callback, $before = null) {
		$this->addRoute('POST', $route, $callback, $before);
	}
	/**
	 * Shorthand function for put requests
	 * @param string $route    the route we want to match
	 * @param mixed  $callback the callback called when the route is matched
	 * @param mixed  $before   the middleware called before if the route is matched
	 */
	public function put($route, $callback, $before = null) {
		$this->addRoute('PUT', $route, $callback, $before);
	}
	/**
	 * Shorthand function for delete requests
	 * @param string $route    the route we want to match
	 * @param mixed  $callback the callback called when the route is matched
	 * @param mixed  $before   the middleware called before if the route is matched
	 */
	public function delete($route, $callback, $before = null) {
		$this->addRoute('DELETE', $route, $callback, $before);
	}

	private function fixRoute($route) {
		// force slashes on both sides
		$route = '/'.trim($route,'/').($route=='/'?'':'/');
		// prepend group route if we can
		if($this->group['route'] != null) {
			$route = rtrim($this->group['route'], '/').$route;
		}
		return $route;
	}

	/**
	 * Adds a parsed route to the collection with its callback and middleware(s)
	 * @param string $method   the method that should match this route
	 * @param string $route    the route we want to match
	 * @param mixed  $callback the callback called when the route is matched
	 * @param mixed  $before   the middleware called before if the route is matched
	 */
	private function addRoute($method, $route, $callback, $before) {
		$route = $this->fixRoute($route);

		// if dynamic route, parse, else just make it regex friendly
		if(strpos($route, ':')) {
			$route = static::parseRoute($route);
		} else $route = str_replace('/', '\/', $route);

		// check for controller methods
		$callback = static::processCallback($callback);

		// check for middleware methods
		if(!is_null($before) && !is_array($before)) $before = [$before];
		if(!is_null($this->group['before']) && !is_array($this->group['before'])) {
			$this->group['before'] = [$this->group['before']];
		}
		if(!is_null($this->group['before'])) {
			foreach ($this->group['before'] as &$groupmiddleware) {
				$groupmiddleware = static::processCallback($groupmiddleware);
			}
		}
		if(!is_null($before)) {
			foreach ($before as &$middleware) {
				$middleware = static::processCallback($middleware);
			}
		}

		// add route to collection
		static::$routes[$method][$route] = [
			'callback' => $callback,
			'before' => []
		];

		// add group middleware if we can
		if($this->group['before'] != null) {
			foreach ($this->group['before'] as $groupmiddleware) {
				static::$routes[$method][$route]['before'][] = $groupmiddleware;
			}
		}
		if($before != null) {
			foreach ($before as $middleware) {
				static::$routes[$method][$route]['before'][] = $middleware;
			}
		}
	}

	/**
	 * Set a group then walk through a callback of routes to apply it
	 * @param string   $route    the route prefix
	 * @param callback $callback the callback of routes
	 * @param mixed    $before   the group middleware
	 */
	public function group($route, $callback, $before = null) {
		$route = $this->fixRoute($route);
		$callback(new self($route, $before));
	}

	/**
	 * turns a controller/middleware call to a callback-able array if needed
	 * @param  mixed $callback the callback to process
	 * @return mixed           a closure or a class/function callback array
	 */
	private static function processCallback($callback) {
		if(is_array($callback) || is_null($callback)) return $callback;
		if(!is_callable($callback)) {
			if(strpos($callback, '@')) {
				list($class, $function) = explode('@', $callback);
			} else list($class, $function) = [$callback, 'handle'];
				if(method_exists($class, $function)) {
					$callback = [$class, $function];
				}
		}
		return $callback;
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

		// if static method, callback and bail out
		if(array_key_exists($static_uri = str_replace('/', '\/', CURRENT_URI), static::$routes[static::$method])) {
			$found = true;
			foreach (static::$routes[static::$method][$static_uri]['before'] as $middleware) {
				call_user_func($middleware);
			}
			call_user_func(static::$routes[static::$method][$static_uri]['callback']);

		// else try looping through the table and match a regex
	} else foreach (static::$routes[static::$method] as $route => $callbacks) {
			$callback = $callbacks['callback'];
			$before = $callbacks['before'];
			// if match then callback and bail out
			if(preg_match("/^$route$/", CURRENT_URI, $arguments)) {
				$found = true;
				// remove non custom matches
				$arguments = array_filter($arguments, function($key) {
					return !is_numeric($key);
				}, '2');

				foreach ($before as $middleware) {
					call_user_func_array($middleware, $arguments);
				}

				call_user_func_array($callback, $arguments);
				break;
			}
		}

		// if not found display 404
		if(!$found) {
			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
			if(is_callable(static::$notfound)) {
				call_user_func(static::$notfound);
			} else echo '404 Not Found';
			exit;
		}
	}
}
