<?php
namespace Mortar\Mortar;

use Mortar\Foundation\Traits\Singleton;
use Mortar\Foundation\Tools\Debug;

use Mortar\Mortar\Http\Router;

class Mortar extends Singleton {
	private $views;

	/**
	 * Instanciate the paths with config values
	 * Start capturing the output used for debug
	 */
	protected function __construct($params) {
		ob_start();
		$this->views = [
			'templates' => isset($params['views']['templates'])?$params['views']['templates']:VIEWS_TEMPLATES,
			'compiled' => isset($params['views']['compiled'])?$params['views']['compiled']:VIEWS_COMPILED
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
		if(!is_dir(path($path))) {
			echo "Warning, folder $path does not exist.";
			return;
		}
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
