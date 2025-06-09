<?php
defined("BASEPATH") OR exit("No direct scripts allowed");

if(!function_exists("fast_dump")){
  function fast_dump($data){
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    exit;
  }
}

if(!function_exists("get_current_module")){
  function get_current_module($modules = []){
    if(!$modules){
      exit("Couldn't find key 'modules' in config");
    }

    $server     = $_SERVER["SERVER_NAME"];
    $subdomain  = explode(".", $server)[0];

    if(!in_array($subdomain, $modules)){
      exit("Module $subdomain doesn't exists");
    }

    return $subdomain;
  }
}
