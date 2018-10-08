<?php
namespace Mortar\Mortar\Build;

use Mortar\Foundation\Traits\Singleton;

class Parser extends Singleton {

	private $variables;

	public function loadVariables($variables) {
		$this->variables = $variables;
	}

	public function parse($template, $stamp = null) {
		$regex = '/('.PARSER_OPEN.'((?>[^'.PARSER_MASK.']|(?1))*)'.PARSER_STOP.')/sm';
		$parsed = preg_replace_callback($regex, function($matches) {
			$params = explode('|', $matches[2]);
			$callback = array_shift($params);

			if(is_callable([$this, $callback])) {
				return $this->$callback(...$params);
			} else return htmlspecialchars($matches[1]);
		}, $template);

		return (is_null($stamp)?'':"<?php#$stamp?>\n").$parsed;
	}

	private function var($var) {
		return '<?=escape($this->variables[$var])?>';
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
