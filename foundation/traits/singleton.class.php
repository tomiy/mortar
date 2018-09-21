<?php
namespace Mortar\Foundation\Traits;

abstract class Singleton {

	/**
	 * Instance of the singleton
	 * @var object
	 */
	protected static $instance;

	/**
	 * Protected function as to get inherited but still not get called by external
	 */
	protected function __construct() {}

	/**
	 * Create the instance if necessary then return it
	 * @return object the instance
	 */
	public static function getInstance($params = null) {
		if(empty(static::$instance)) static::$instance = new static($params);
		return static::$instance;
	}
}
