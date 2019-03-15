<?php
namespace Mortar\Mortar\Display;

use Mortar\Mortar\Core;

class Controller {
    protected $mortar;

    public function __construct($mortar) {
        $this->mortar = $mortar;
    }
}
