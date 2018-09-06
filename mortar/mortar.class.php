<?php
namespace Mortar;

use Foundation\Traits\Singleton;
use Foundation\Tools\Debug;

use Mortar\Http\Router;

class Mortar extends Singleton {

	/**
	 * Start capturing the output used for debug
	 */
	protected function __construct() {
		ob_start();
	}

	/**
	 * Capture the debug and display the content
	 * @param  boolean $debug should we return the debug
	 * @return string         the debug
	 */
	public function display($debug = false) {
    $error_reporting = ob_get_contents();
    ob_end_clean();

		//display

    echo $debug?$error_reporting:null;
  }
}
