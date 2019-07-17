<?php

namespace Mortar\Foundation\Tools;

abstract class DependencyInjector
{
    private static $map = [];
    private static $factories = [];
    private static $parameters = [];
    private static $objects = [];

    public static function set($alias, $closure, $isfactory = false)
    {
        self::$map[$alias] = $closure;
        self::$factories[$alias] = $isfactory;
    }

    public static function get($alias)
    {
        if (!isset(self::$objects[$alias]) && !self::$factories[$alias]) {
            self::$objects[$alias] = self::$map[$alias]();
        }
        return self::$objects[$alias];
    }

    public static function setParameter($alias, $parameter)
    {
        self::$parameters[$alias] = $parameter;
    }

    public static function getParameter($alias)
    {
        return self::$parameters[$alias];
    }
}
