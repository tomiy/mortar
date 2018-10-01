<?php
namespace Mortar\Mortar\Build;

class Parser {

	private $variables;

	public function __construct($variables) {
		$this->variables = $variables;
	}

	public function parse($template, $stamp) {
		$parsed = "<?php#$stamp?>\n$template";

		return $parsed;
	}
}
