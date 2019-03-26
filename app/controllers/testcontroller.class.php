<?php
namespace Mortar\App\Controllers;

use Mortar\Engine\Core;
use Mortar\Engine\Display\Controller;

class TestController extends Controller {

    public function test() {
        echo 'test controller';
    }

    public function key($key) {
        echo "key: $key";
    }

}
