<?php
namespace Mortar\Mortar\Build;

use Mortar\Foundation\Traits\Singleton;

class Parser extends Singleton {

	private $variables;

	protected function __construct($variables) {
		$this->variables = $variables;
	}

	public function parse($template, $stamp = null) {
		$parsed = preg_replace_callback('/<!(.*)!>/m', function($matches) {
			$params = explode('|', $matches[1]);
			$callback = array_shift($params);

			if(is_callable([$this, $callback])) {
				return $this->$callback(...$params);
			} else return $matches[0];
		}, $template);

		return "<?php#$stamp?>\n$parsed";
	}

	private function csrf() {
		return '<input type="hidden" name="token" value="<?= hash_hmac(\'sha256\', CURRENT_URI, $_SESSION[\'csrf_token\']); ?>"/>';
	}
}
