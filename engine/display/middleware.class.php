<?php
namespace Mortar\Engine\Display;

class Middleware {
    protected $request;

    public function __construct($request) {
        $this->request = $request;
    }

    public function handle() {}
}
