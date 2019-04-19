<?php
namespace Mortar\Engine\Http;

use Mortar\Engine\Core;
use Mortar\Engine\Http\RouteWorker;
use Mortar\Engine\Http\RouteResponse;

class Router {
    /**
     * The routes + attached middleware
     * @var array
     */
    private $routes = [];
    private $response;

    private $mortar;
    private $worker;

    private $scope;

    /**
     * Instanciate a new router
     * @param string $prefix the route group
     * @param mixed  $before the group middleware
     */
    public function __construct($mortar, $worker, $response) {
        $this->mortar = $mortar;
        $this->worker = $worker;
        $this->response = $response;
        $this->scope = 'Mortar\App\\';
    }

    public function getScope() {
        return $this->scope;
    }

    public function setScope($scope) {
        $this->scope = $scope;
    }

    public function routes() {
        return $this->routes;
    }

    /**
     * Sets the 404 callback
     * @var callback
     */
    public function setNotFound($callback) {
        $this->response->setNotFound($callback);
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

    /**
     * Adds a parsed route to the collection with its callback and middleware(s)
     * @param string $method   the method that should match this route
     * @param string $route    the route we want to match
     * @param mixed  $callback the callback called when the route is matched
     * @param mixed  $before   the middleware called before if the route is matched
     */
    private function addRoute($method, $route, $callback, $before) {
        $route = $this->worker->fixRoute($route);

        // if dynamic route, parse, else just make it regex friendly
        if(strpos($route, ':')) {
            $route = $this->worker->parseRoute($route);
        } else $route = str_replace('/', '\/', $route);

        // check for controller methods
        $callback = $this->worker->processCallback($callback);

        // check for middleware methods
        $before = $this->worker->processMiddlewares($before);

        // add route to collection
        $this->routes[$method][$route] = [
            'callback' => $callback,
            'before' => []
        ];

        // add group middleware if we can
        $this->routes[$method][$route]['before'] = $this->worker->addMiddlewares($before);
    }

    /**
     * Set a group then walk through a callback of routes to apply it
     * @param string   $route    the route prefix
     * @param callback $callback the callback of routes
     * @param mixed    $before   the group middleware
     */
    public function group($route, $callback, $before = null) {
        $this->worker->pushContext($route, $before);
        $callback($this);
        $this->worker->popContext();
    }

    /**
     * try to match the uri to a route, and launch callbacks as applicable
     */
    public function dispatch() {
        $found = false;

        // if static method, callback and bail out
        if(array_key_exists(
            $static_uri = str_replace('/', '\/', CURRENT_URI),
            $this->routes[$this->response->getMethod()])
        ) {
            $found = true;
            foreach ($this->routes[$this->response->getMethod()][$static_uri]['before'] as $middleware) {
                call_user_func($middleware);
            }
            call_user_func($this->routes[$this->response->getMethod()][$static_uri]['callback']);

        // else try looping through the table and match a regex
    } else foreach ($this->routes[$this->response->getMethod()] as $route => $callbacks) {
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
            $this->response->notFound();
        }
    }
}
