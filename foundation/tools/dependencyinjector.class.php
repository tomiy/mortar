<?php
namespace Mortar\Foundation\Tools;

use Mortar\Foundation\Traits\Singleton;

class DependencyInjector extends Singleton {
    private $map;

    protected function __construct() {
        $this->map = [];
    }

    public function set($alias, $closure) {
        $this->map[$alias] = $closure;
    }

    public function get($alias) {
        return $this->map[$alias]($this);
    }
}
