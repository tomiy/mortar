<?php
namespace Mortar\Mortar\Http;

use Mortar\Mortar\Core;

class RouteWorker {

    /**
     * The shortcuts used to parse to regex (makes route writing much easier)
     * @var array
     */
    private $shorthands = [
        'int' => '\d',
        'str' => '[a-zA-Z-]',
        'all' => '[\w-]'
    ];

    private $mortar;

    private $routectx;
    private $middlewarectx;

    public function __construct($mortar) {
        $this->mortar = $mortar;

        $this->routectx = [];
        $this->middlewarectx = [];
    }

    public function pushContext($routectx, $middlewarectx) {
        array_push($this->routectx, $routectx);
        array_push($this->middlewarectx, $this->processMiddlewares($middlewarectx));
    }

    public function popContext() {
        array_pop($this->routectx);
        array_pop($this->middlewarectx);
    }

    public function processMiddlewares($before) {
        if(!is_null($before)) {
            if(!is_array($before)) $before = [$before];
            foreach ($before as &$groupmiddleware) {
                $groupmiddleware = $this->processCallback($groupmiddleware);
            }
        }

        return $before;
    }

    public function addMiddlewares($before) {
        $output = [];
        if($this->middlewarectx != null) {
            foreach (call_user_func_array('array_merge', $this->middlewarectx) as $groupmiddleware) {
                $output[] = $groupmiddleware;
            }
        }
        if($before != null) {
            foreach ($before as $middleware) {
                $output[] = $middleware;
            }
        }

        return $output;
    }

    public function fixRoute($route) {
        $route = '/'.trim($route,'/').($route=='/'?'':'/');
        if($this->routectx) $route = rtrim(implode('/', $this->routectx), '/').$route;
        return $route;
    }

    /**
     * Parses the route into a nice, matchable regex
     * @param  string $route the route we want to parse
     * @return string        the parsed route
     */
    public function parseRoute($route) {
        // check if we're at top level (recursion has slashes trimmed)
        $parsedRoute = $route[0] == '/'?'\/':'';
        // get route parts (array_filter to trim blank values)
        $aRoute = $remainingRoute = array_filter(explode('/', $route));
        foreach($aRoute as $routePart) {
            // if optional part
            if(strpos($routePart, '?')) {
                // go into recursion, embed the rest of the route into an optional regex
                $remainingRoute[0] = str_replace('?', '', $routePart);
                $parsedRoute .= '('.$this->parseRoute(implode('/', $remainingRoute)).')?';
                break;
            }
            // if the route part is dynamic
            if(strpos($routePart, ':')) {
                list($pattern, $name) = explode(':', $routePart);
                // replace the shorthands with regex
                $pattern = str_replace(
                    array_keys($this->shorthands),
                    array_values($this->shorthands),
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
     * turns a controller/middleware call to a callback-able array if needed
     * @param  mixed $callback the callback to process
     * @return mixed           a closure or a class/function callback array
     */
    public function processCallback($callback) {
        if(is_array($callback) || is_null($callback)) return $callback;
        if(!is_callable($callback)) {
            if(strpos($callback, '@')) {
                list($class, $function) = explode('@', $callback);
            } else list($class, $function) = [$callback, 'handle'];
                if(method_exists($class, $function)) {
                    $callback = [new $class($this->mortar), $function];
                }
        }
        return $callback;
    }
}
