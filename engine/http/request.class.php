<?php

namespace Mortar\Engine\Http;

class Request
{
    public $get;
    public $post;
    public $session;
    public $cookie;
    public $server;

    public function __construct($get, $post, $session, $cookie, $server)
    {
        $this->get = $get;
        $this->post = $post;
        $this->session = $session;
        $this->cookie = $cookie;
        $this->server = $server;
    }

    public function pushToSession($index, $value)
    {
        $this->session[$index] = $value;
        $_SESSION[$index] = $value;
    }
}
