<?php
namespace Mortar\Foundation\Tools;

use Mortar\Foundation\Traits\Singleton;

class DependencyInjector extends Singleton {
    private $map;
    private $objects;

    protected function __construct() {
        $this->map = [
            'closures' => [],
            'parameters' => []
        ];
    }

    public function set($alias, $closure) {
        $this->map['closures'][$alias] = $closure;
    }

    public function get($alias) {
        return $this->map['closures'][$alias]($this);
    }

    public function setParameter($alias, $parameter) {
        $this->map['parameters'][$alias] = $parameter;
    }

    public function getParameter($alias) {
        return $this->map['parameters'][$alias];
    }
}
