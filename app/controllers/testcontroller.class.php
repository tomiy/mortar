<?php
namespace Mortar\App\Controllers;

class TestController {

	public function test() {
		echo 'test controller';
	}

	public function key($key) {
		echo escape("key: $key");
	}

}
