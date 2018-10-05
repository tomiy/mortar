<?php
namespace Mortar\Mortar\Build;

use Mortar\Foundation\Traits\Singleton;

class Parser extends Singleton {

	private $variables;

	protected function __construct($variables) {
		$this->variables = $variables;
	}

	public function parse($template, $stamp = null) {
		$parsed = preg_replace_callback('/'.PARSER_OPEN.'(.*)'.PARSER_STOP.'/sm', function($matches) {
			$params = explode('|', $matches[1]);
			$callback = array_shift($params);

			if(is_callable([$this, $callback])) {
				return $this->$callback(...$params);
			} else return $matches[0];
		}, $template);

		return (is_null($stamp)?'':"<?php#$stamp?>\n").$parsed;
	}

	private function var($var) {
		return $this->variables[$var];
	}

	private function loop($counter, $content) {
		$counter = $this->parse($counter);
		$content = $this->parse($content);
		$output = '';

		for ($i = 0; $i < $counter; $i++) {
			$output .= $content;
		}

		return $output;
	}

	private function csrf() {
		return '<input type="hidden" name="_token" value="<?= hash_hmac(\'sha256\', CURRENT_URI, $_SESSION[\'csrf_token\']); ?>"/>';
	}
}
