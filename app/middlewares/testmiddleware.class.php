<?php
namespace Mortar\App\Middlewares;

use Mortar\Engine\Display\Middleware;

class TestMiddleware extends Middleware {

    public function handle() {
        echo '1st ';
    }

}
