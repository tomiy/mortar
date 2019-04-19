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

        $this->objects = [];
    }

    public function set($alias, $closure, $isfactory = false) {
        $this->map['closures'][$alias] = [$isfactory, $closure];
    }

    public function get($alias) {
        if(
            !isset($this->objects[$alias]) &&
            !$this->map['closures'][$alias][0]
        ) {
            $this->objects[$alias] = $this->map['closures'][$alias][1]($this);
        }
        return $this->objects[$alias];
    }

    public function setParameter($alias, $parameter) {
        $this->map['parameters'][$alias] = $parameter;
    }

    public function getParameter($alias) {
        return $this->map['parameters'][$alias];
    }
}
