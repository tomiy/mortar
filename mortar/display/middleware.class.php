<?php
namespace Mortar\Mortar\Display;

use Mortar\Mortar\Core;

class Middleware {
    protected $mortar;

    public function __construct($mortar) {
        $this->mortar = $mortar;
    }

    public function handle() {}
}
