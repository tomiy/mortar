<?php
namespace Mortar;

use Foundation\Traits\Singleton;

class Mortar extends Singleton {

  protected function __construct() {
    ob_start();
  }
}
