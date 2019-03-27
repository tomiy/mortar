<?php
namespace Mortar\Engine\Display;

use Mortar\Engine\Core;

class Model {
    protected $mortar;

    protected $table;

    public function __construct($mortar) {
        $this->mortar = $mortar;
    }
}
