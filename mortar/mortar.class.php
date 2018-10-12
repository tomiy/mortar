<?php
namespace Mortar\Mortar;

use Mortar\Foundation\Traits\Singleton;

use Mortar\Mortar\Http\Router;
use Mortar\Mortar\Build\Parser;

class Mortar extends Singleton {
	private $views;

	private $parser;
	private $template;
	private $variables;

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

		$this->parser = new Parser($this);
		$this->variables = [];
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
			echo escape("Warning, folder $path does not exist.");
			return;
		}
		$this->views[$key] = realpath($path).DS;
	}

	public function assign($arguments) {
		foreach($arguments as $name => $value) {
			$this->variables[$name] = $value;
		}
	}

	public function view($name) {
		$this->template = $name;
	}

	private function compile($tpl) {
		$tplContents = file_get_contents($tplPath = $this->tplpath().$tpl.VIEWS_EXTENSION);
		if(
			!file_exists($cmpPath = $this->cmppath().md5($tpl).".php") ||
			fgets(fopen($cmpPath, 'r')) != '<?php#'.filemtime($tplPath)."?>\n"
		) {
			$compiled = $this->parser->parse($tplContents, filemtime($tplPath));
			file_put_contents($cmpPath, $compiled);
		}

		return $cmpPath;
	}

	/**
	 * Capture the debug and display the content
	 * @param  boolean $debug should we return the debug
	 * @return string         the debug
	 */
	public function display($debug = false) {
		$errorReporting = ob_get_contents();
		ob_end_clean();

		$this->parser->loadVariables($this->variables);

		//display
		if($this->template) {
			$compiled = $this->compile($this->template);
			include $compiled;
		}

		echo $debug?$errorReporting:null;
	}
}
