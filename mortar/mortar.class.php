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
	protected function __construct($params = null) {
		ob_start();
		$this->setTemplatesPath(
			isset($params['views']['templates'])?
				$params['views']['templates']:
				VIEWS_TEMPLATES
		);
		$this->setCompiledPath(
			isset($params['views']['compiled'])?
				$params['views']['compiled']:
				VIEWS_COMPILED
		);
	}

	//shorthands
	public function tplpath() { return $this->views['templates']; }
	public function cmppath() { return $this->views['compiled']; }

	public function setTemplatesPath($path) {
		$this->setViewPath('templates', $path);
	}

	public function setCompiledPath($path) {
		$this->setViewPath('compiled', $path);
	}

	private function setViewPath($key, $path) {
		if(!is_dir(realpath($path))) {
			echo "Warning, folder $path does not exist.";
			return;
		}
		$this->views[$key] = realpath($path).DS;
	}

	private function compile($tpl) {
    $tplContents = file_get_contents($tplPath = $this->tplpath().$tpl.VIEWS_EXTENSION);
		if(
			!file_exists($cmpPath = $this->cmppath().md5($tpl).".php") ||
			fgets(fopen($cmpPath, 'r')) != '<?php#'.filemtime($tplPath)."?>\n"
		) {
			echo "Compiling $tplPath";
			//TODO: parse xd
			//vvv this is just placeholder so that it doesn't compile everytime
			$tplContents = '<?php#'.filemtime($tplPath)."?>\n".$tplContents;
			file_put_contents($cmpPath, $tplContents);
		}
  }

	/**
	 * Capture the debug and display the content
	 * @param  boolean $debug should we return the debug
	 * @return string         the debug
	 */
	public function display($debug = false) {
    $errorReporting = ob_get_contents();
    ob_end_clean();

		//display
		$this->compile('testtemplate');

    echo $debug?$errorReporting:null;
  }
}
