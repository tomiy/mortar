<?php
namespace Mortar\App\Controllers;

use Mortar\Mortar\Core;
use Mortar\Mortar\Display\Controller;

class TestController extends Controller {

    public function test() {
        echo 'test controller';
    }

    public function key($key) {
        echo "key: $key";
    }

}
