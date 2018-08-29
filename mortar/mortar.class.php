<?php
namespace Mortar;

use Foundation\Traits\Singleton;
use Foundation\Tools\Debug;

use Mortar\Http\Router;

class Mortar extends Singleton {

	protected function __construct() {
		ob_start();
	}

	public function display($debug = false) {
    $error_reporting = ob_get_contents();
    ob_end_clean();

		//display

    echo $debug?$error_reporting:null;
  }
}
