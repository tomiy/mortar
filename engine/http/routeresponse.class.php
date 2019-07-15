<?php
namespace Mortar\Engine\Http;

class RouteResponse {
    /**
     * The allowed methods (used to check for forced method via $_POST)
     * @var array
     */
    private static $methods = ['GET', 'POST', 'PUT', 'DELETE'];

    private $request;
    private $method;
    private $notfound;

    public function getMethod() {
        return $this->method;
    }

    public function getRequest() {
        return $this->request;
    }

    public function setNotFound($notfound) {
        $this->notfound = $notfound;
    }

    public function __construct($request) {

        $this->request = $request;

        $this->method = isset($request->post['_method'])
            && in_array(strtoupper($request->post['_method']), static::$methods)
            ?strtoupper($request->post['_method']):strtoupper($request->server['REQUEST_METHOD']);
    }

    public function notFound() {
        header($this->request->server["SERVER_PROTOCOL"]." 404 Not Found");
        if(is_callable($this->notfound)) {
            call_user_func($this->notfound);
        } else echo '404 Not Found';
    }
}
