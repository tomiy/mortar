<?php
namespace Mortar\Mortar\Http;

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

	public function setNotFound($notfound) {
		$this->notfound = $notfound;
	}

	public function __construct($request) {

		$this->request = $request;

		$this->method = in_array(strtoupper($request->post['_method']), static::$methods))
			?strtoupper($request->post['_method']):strtoupper($request->server['REQUEST_METHOD']);

		if($this->method != 'GET') $this->checkCSRF($token);
	}

	public function checkCSRF($token) {
		$calc = hash_hmac('sha256', CURRENT_URI, $this->request->session['csrf_token']);
		if (!hash_equals($calc, $token) || !in_array($this->method, static::$methods)) {
			header($this->request->server["SERVER_PROTOCOL"]." 403 Forbidden");
			exit;
		}
	}

	public function notFound() {
		header($this->request->server["SERVER_PROTOCOL"]." 404 Not Found");
		if(is_callable($this->notfound)) {
			call_user_func($this->notfound);
		} else echo '404 Not Found';
		exit;
	}
}
