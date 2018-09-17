<?php
namespace Mortar;

use Foundation\Traits\Singleton;
use Foundation\Tools\Debug;

use Mortar\Http\Router;

class Mortar extends Singleton {
	private $views;

	/**
	 * Instanciate the paths with config values
	 * Start capturing the output used for debug
	 */
	//TODO (maybe) a param array to set paths and other shit directly
	protected function __construct() {
		ob_start();
		$this->views = [
			'templates' => VIEWS_TEMPLATES,
			'compiled' => VIEWS_COMPILED
		];
	}

	//shorthands
	public function tpl() { return $this->views['templates']; }
	public function cmp() { return $this->views['compiled']; }

	public function setTemplatesPath($path) {
		$this->setViewPath('templates', $path);
	}

	public function setCompiledPath($path) {
		$this->setViewPath('compiled', $path);
	}

	private function setViewPath($key, $path) {
		//TODO: folder checks, relative paths
		$this->views[$key] = $path;
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
