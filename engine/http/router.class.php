<?php

namespace Mortar\Engine\Http;

class Router
{
    /**
     * The routes + attached middleware
     * @var array
     */
    private $routes = [];
    private $response;

    private $worker;

    /**
     * Instanciate a new router
     * @param RouteWorker $worker the route worker parses routes and processes middleware calls
     * @param RouteResponse $response the route response holds the http request and method
     */
    public function __construct($worker, $response)
    {
        $this->worker = $worker;
        $this->response = $response;
    }

    public function routes()
    {
        return $this->routes;
    }

    /**
     * Sets the 404 callback
     * @var callback
     */
    public function setNotFound($callback)
    {
        $this->response->setNotFound($callback);
    }

    /**
     * Shorthand function for get requests
     * @param string $route    the route we want to match
     * @param mixed  $callback the callback called when the route is matched
     * @param mixed  $middlewares   the middlewares called if the route is matched
     */
    public function get($route, $callback, $middlewares = null)
    {
        $this->addRoute('GET', $route, $callback, $middlewares);
    }
    /**
     * Shorthand function for post requests
     * @param string $route    the route we want to match
     * @param mixed  $callback the callback called when the route is matched
     * @param mixed  $middlewares   the middleware called if the route is matched
     */
    public function post($route, $callback, $middlewares = null)
    {
        $this->addRoute('POST', $route, $callback, $middlewares);
    }
    /**
     * Shorthand function for put requests
     * @param string $route    the route we want to match
     * @param mixed  $callback the callback called when the route is matched
     * @param mixed  $middlewares   the middleware called if the route is matched
     */
    public function put($route, $callback, $middlewares = null)
    {
        $this->addRoute('PUT', $route, $callback, $middlewares);
    }
    /**
     * Shorthand function for delete requests
     * @param string $route    the route we want to match
     * @param mixed  $callback the callback called when the route is matched
     * @param mixed  $middlewares   the middleware called if the route is matched
     */
    public function delete($route, $callback, $middlewares = null)
    {
        $this->addRoute('DELETE', $route, $callback, $middlewares);
    }

    /**
     * Adds a parsed route to the collection with its callback and middleware(s)
     * @param string $method   the method that should match this route
     * @param string $route    the route we want to match
     * @param mixed  $callback the callback called when the route is matched
     * @param mixed  $middlewares   the middleware called if the route is matched
     */
    private function addRoute($method, $route, $callback, $middlewares)
    {
        $route = $this->worker->fixRoute($route);

        // if dynamic route, parse, else just make it regex friendly
        if (strpos($route, ':')) {
            $route = $this->worker->parseRoute($route);
        } else $route = str_replace('/', '\/', $route);

        // check for controller methods
        $callback = $this->worker->processCallback($callback, $this->response->getRequest());

        // check for middleware methods
        $middlewares = $this->worker->processMiddlewares($middlewares, $this->response->getRequest());

        // add route to collection
        $this->routes[$method][$route] = [
            'callback' => $callback,
            'middlewares' => []
        ];

        // add group middleware if we can
        $this->routes[$method][$route]['middlewares'] = $this->worker->addMiddlewares($middlewares);
    }

    /**
     * Set a group then walk through a callback of routes to apply it
     * @param string   $route    the route prefix
     * @param callback $callback the callback of routes
     * @param mixed    $middlewares   the group middleware
     */
    public function group($route, $callback, $middlewares = null)
    {
        $this->worker->pushContext($route, $middlewares, $this->response->getRequest());
        $callback($this);
        $this->worker->popContext();
    }

    /**
     * try to match the uri to a route, and launch callbacks as applicable
     */
    public function dispatch()
    {
        $found = false;

        // if static method, callback and bail out
        if (array_key_exists(
            $static_uri = str_replace('/', '\/', CURRENT_URI),
            $this->routes[$this->response->getMethod()]
        )) {
            $found = true;
            foreach ($this->routes[$this->response->getMethod()][$static_uri]['middlewares'] as $middleware) {
                call_user_func($middleware);
            }
            call_user_func($this->routes[$this->response->getMethod()][$static_uri]['callback']);

            // else try looping through the table and match a regex
        } else foreach ($this->routes[$this->response->getMethod()] as $route => $callbacks) {
            $callback = $callbacks['callback'];
            $middlewares = $callbacks['middlewares'];
            // if match then callback and bail out
            if (preg_match("/^$route$/", CURRENT_URI, $arguments)) {
                $found = true;
                // remove non custom matches
                $arguments = array_filter($arguments, function ($key) {
                    return !is_numeric($key);
                }, '2');


                foreach ($middlewares as $middleware) {
                    call_user_func($middleware);
                }

                call_user_func_array($callback, $arguments);
                break;
            }
        }

        // if not found display 404
        if (!$found) {
            $this->response->notFound();
        }
    }
}
