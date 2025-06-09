<?php
defined("BASEPATH") OR exit("No direct scripts allowed");

if(file_exists(APPPATH . "config" . DIRECTORY_SEPARATOR . "autoload.php")){
  require APPPATH . "config" . DIRECTORY_SEPARATOR . "autoload.php";
}else{
  exit("application/config/autoload.php doesn't exists");
}

if(isset($config["global_configs"])){
  foreach ($config["global_configs"] as $item) {
    if(!file_exists(APPPATH . "config" . DIRECTORY_SEPARATOR . "$item.php")){
      exit("Could'nt find file ($item.php) you've specified in application/configs");
    }

    require_once APPPATH . "config" . DIRECTORY_SEPARATOR . "$item.php";
  }
}

if(isset($config["global_helpers"])){
  foreach ($config["global_helpers"] as $item) {
    if(!file_exists(APPPATH . "helpers" . DIRECTORY_SEPARATOR . "$item.php")){
      exit("Could'nt find file ($item.php) you've specified in application/helpers");
    }

    require_once APPPATH . "helpers" . DIRECTORY_SEPARATOR . "$item.php";
  }
}

if(isset($config["core"])){
  foreach ($config["core"] as $item) {
    if(!file_exists(BASEPATH . "core" . DIRECTORY_SEPARATOR . "$item.php")){
      exit("Could'nt find file ($item.php) you've specified in core");
    }

    require_once BASEPATH . "core" . DIRECTORY_SEPARATOR . "$item.php";
  }
}

$config["current_module"] = get_current_module($config["modules"] ?? []);

if(!is_dir(BASEPATH . "modules" . DIRECTORY_SEPARATOR . $config["current_module"])){
  exit("Couldn't find module directory for '{$config["current_module"]}'");
}

if(!is_dir(BASEPATH . "assets" . DIRECTORY_SEPARATOR . $config["current_module"])){
  exit("Couldn't find asset directory for '{$config["current_module"]}'");
}

define("ASSETSPATH",  BASEPATH . "assets"  . DIRECTORY_SEPARATOR . $config["current_module"] . DIRECTORY_SEPARATOR);
define("MODULEPATH",  BASEPATH . "modules" . DIRECTORY_SEPARATOR . $config["current_module"] . DIRECTORY_SEPARATOR);

if(file_exists(MODULEPATH . "config" . DIRECTORY_SEPARATOR . "autoload.php")){
  require MODULEPATH . "config" . DIRECTORY_SEPARATOR . "autoload.php";
}else{
  exit("{$config["current_module"]}/config/autoload.php doesn't exists");
}

if(isset($config["configs"])){
  foreach ($config["configs"] as $item) {
    if(!file_exists(MODULEPATH . "config" . DIRECTORY_SEPARATOR . "$item.php")){
      exit("Could'nt find file ($item.php) you've specified in application/configs");
    }

    require_once MODULEPATH . "config" . DIRECTORY_SEPARATOR . "$item.php";
  }
}

if(!file_exists(BASEPATH . "core" . DIRECTORY_SEPARATOR . "Route.php")){
  exit("core/Route.php doesn't exists");
}

if(!is_dir(MODULEPATH . "routes")){
  exit("{$config["current_module"]}/routes folder doesn't exists");
}

if(!file_exists(BASEPATH . "core" . DIRECTORY_SEPARATOR . "Controller.php")){
  exit("core/Controller.php doesn't exists");
}

require_once BASEPATH . "core" . DIRECTORY_SEPARATOR . "Controller.php";

Controller::$s_config = $config;

$routes = glob(MODULEPATH . "routes" . DIRECTORY_SEPARATOR . "*_route.php");

if(!$routes){
  exit("Couldn't find any route file inside of routes folder in {$config["current_module"]}/routes");
}

foreach ($routes as $item) {
  require_once $item;
}

Route::action();
