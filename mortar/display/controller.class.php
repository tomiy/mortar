<?php
namespace Mortar\Mortar\Display;

use Mortar\Mortar\Mortar;

class Controller {
    private $mortar;

    public function __construct($mortar) {
        $this->mortar = $mortar;
    }
}
