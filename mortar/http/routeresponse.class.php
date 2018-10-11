<?php
namespace Mortar\Mortar\Http;

class RouteResponse {
	/**
	 * The allowed methods (used to check for forced method via $_POST)
	 * @var array
	 */
	private static $methods = ['GET', 'POST', 'PUT', 'DELETE'];

	private $method;
	private $notfound;

	public function getMethod() {
		return $this->method;
	}

	public function setNotFound($notfound) {
		$this->notfound = $notfound;
	}

	public function __construct($method, $token) {
		$this->method = in_array(strtoupper($method), static::$methods))
			?strtoupper($method):strtoupper($_SERVER['REQUEST_METHOD']);

		if($this->method != 'GET') $this->checkCSRF($token);
	}

	public function checkCSRF($token) {
		$calc = hash_hmac('sha256', CURRENT_URI, $_SESSION['csrf_token']);
		if (!hash_equals($calc, $token) || !in_array(static::$method, static::$methods)) {
			header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
			exit;
		}
	}

	public function notFound() {
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
		if(is_callable($this->$notfound)) {
			call_user_func($this->$notfound);
		} else echo '404 Not Found';
		exit;
	}
}
