<?php

namespace Mortar\Engine\Display;

use Mortar\Foundation\Tools\DependencyInjector as DI;

class Controller
{
    protected $request;
    protected $mortar;

    public function __construct($request)
    {
        $this->request = $request;
        $this->mortar = DI::get('core');
    }
}
