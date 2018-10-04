<?php
namespace Mortar\Mortar\Build;

class Parser extends Singleton {

	private $variables;

	protected function __construct($variables) {
		$this->variables = $variables;
	}

	public function parse($template, $stamp = null) {
		$parsed = preg_replace_callback('/<!(.*)!>/m', function($matches) {
			$params = explode('|', $matches[1]);
			$callback = array_shift($params);

			if(is_callable($callback)) {
				return $this->$callback(...$params);
			} else return $matches[0];
		}, $template);

		return "<?php#$stamp?>\n$parsed";
	}
}
