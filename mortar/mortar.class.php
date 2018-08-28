<?php
namespace Mortar;

use Foundation\Traits\Singleton;
use Foundation\Tools\Debug;

use Mortar\Http\Router;

class Mortar extends Singleton {

	private $router;

	protected function __construct() {
		ob_start();
	}

	public function routes() {
		return Router::routes();
	}

	public function debug($obj) {
		return Debug::show($obj);
	}
}
