<?php
namespace Mortar\App\Middlewares;

use Mortar\Engine\Display\Middleware;
use Mortar\Foundation\Tools\Debug;

class CsrfMiddleware extends Middleware {

    public function handle() {
        $this->generateToken();

        $this->method = isset($this->request->post['_method'])
            && in_array(strtoupper($this->request->post['_method']), static::$methods)
            ?strtoupper($this->request->post['_method']):strtoupper($this->request->server['REQUEST_METHOD']);

        if($this->method != 'GET') {
            $calc = hash_hmac('sha256', CURRENT_URI, $this->request->session['csrf_token']);
            if (!hash_equals($calc, $this->request->post['_token'])) {
                header($this->request->server["SERVER_PROTOCOL"]." 403 Forbidden");
            } else {
                $this->refreshToken();
            }
        }
    }

    private function refreshToken() {
        $this->request->session['csrf_token'] = null;
        $_SESSION['csrf_token'] = null;
        $this->generateToken();
    }
    
    private function generateToken() {
        if (empty($this->request->session['csrf_token'])) {
            $this->request->session['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $this->request->session['csrf_token'];
        }
    }

}
