<?php
namespace Mortar\Mortar\Display;

use Mortar\Mortar\Core;

class Middleware {
    private $mortar;

    public function __construct($mortar) {
        $this->mortar = $mortar;
    }

    public function handle() {}
}
