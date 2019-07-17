<?php

namespace Mortar\Engine\Display;

class Controller
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
}
