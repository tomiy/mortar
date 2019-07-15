<?php
namespace Mortar\App\Controllers;

use Mortar\Engine\Core;
use Mortar\Engine\Display\Controller;

use Mortar\App\Models\TestModel;

class TestController extends Controller {

    public function test() {
        $testmodel = (new TestModel())->find(1);
        nl2br(print_r($testmodel));
    }
}
