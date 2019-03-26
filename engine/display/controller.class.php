<?php
namespace Mortar\Engine\Display;

use Mortar\Engine\Core;

class Controller {
    protected $mortar;

    public function __construct($mortar) {
        $this->mortar = $mortar;
    }
}
