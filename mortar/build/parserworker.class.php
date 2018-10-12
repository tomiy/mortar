<?php
namespace Mortar\Mortar\Build;

class ParserWorker {
	private $parser;
	private $mortar;

	public function __construct($parser, $mortar) {
		$this->parser = $parser;
		$this->mortar = $mortar;
	}

	public function var($var) {
		return '<?=escape($this->variables[\''.$var.'\'])?>';
	}

	public function loop($counter, $content) {
		$counter = $this->parser->parse($counter);
		$content = $this->parser->parse($content);
		$output = '';

		for ($i = 0; $i < $counter; $i++) {
			$output .= $content;
		}

		return $output;
	}

	public function template($name) {
		$cmpPath = $this->mortar->compile($name);
		return "<? include $cmpPath ?>";
	}

	public function csrf() {
		return '<input type="hidden" name="_token" value="<?= hash_hmac(\'sha256\', CURRENT_URI, $_SESSION[\'csrf_token\']); ?>"/>';
	}
}
