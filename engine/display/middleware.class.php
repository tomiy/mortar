<?php
namespace Mortar\Engine\Display;

use Mortar\Engine\Core;

class Middleware {
    protected $mortar;

    public function __construct($mortar) {
        $this->mortar = $mortar;
    }

    public function handle() {}
}
