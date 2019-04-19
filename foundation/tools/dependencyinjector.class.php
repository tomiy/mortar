<?php
namespace Mortar\Foundation\Tools;

abstract class DependencyInjector {
    private static $map = [
        'closures' => [],
        'parameters' => []
    ];
    private static $objects = [];

    public static function set($alias, $closure, $isfactory = false) {
        self::$map['closures'][$alias] = [$isfactory, $closure];
    }

    public static function get($alias) {
        if(
            !isset(self::$objects[$alias]) &&
            !self::$map['closures'][$alias][0]
        ) {
            self::$objects[$alias] = self::$map['closures'][$alias][1]();
        }
        return self::$objects[$alias];
    }

    public static function setParameter($alias, $parameter) {
        self::$map['parameters'][$alias] = $parameter;
    }

    public static function getParameter($alias) {
        return self::$map['parameters'][$alias];
    }
}
