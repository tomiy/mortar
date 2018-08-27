<?php
namespace Foundation\Traits;

abstract class Singleton {

  protected static $instance;

  protected function __construct() {}

  public static function getInstance() {

    if(empty(static::$instance)) static::$instance = new static;
    return static::$instance;
  }
}
