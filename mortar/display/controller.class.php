<?php
namespace Mortar\Mortar\Display;

use Mortar\Mortar\Core;

class Controller {
    private $mortar;

    public function __construct($mortar) {
        $this->mortar = $mortar;
    }
}
