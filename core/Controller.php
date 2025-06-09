<?php

class Controller {
  public static $s_config;
  public $config;

  public function __construct(){
    $this->config = self::$s_config;
  }

  
}
