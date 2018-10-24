<?php
namespace Mortar\App\Middlewares;

use Mortar\Mortar\Mortar;
use Mortar\Mortar\Display\Middleware;

class TestMiddleware extends Middleware {

    public function handle() {
        echo '1st ';
    }

}
