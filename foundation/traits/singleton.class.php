<?php
namespace Mortar\Foundation\Traits;

abstract class Singleton {

	/**
	 * Protected functions as to get inherited but still not get called by external
	 */
	protected function __construct() {}
	protected function __clone() {}
	protected function __sleep() {}
	protected function __wakeup() {}

	/**
	 * Create the instance if necessary then return it
	 * @return object the instance
	 */
	public static function getInstance($params = null) {
		static $instance = false;
		if(empty($instance)) $instance = new static($params);
		return $instance;
	}
}
