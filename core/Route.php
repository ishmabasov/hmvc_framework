<?php

class Route{
  private static $routes = [];

  public static function action(){
    $request_method = $_SERVER["REQUEST_METHOD"];
    $request_uri    = $_SERVER["REQUEST_URI"];

    if(!isset(self::$routes[$request_method][$request_uri])){
      die("hi");
    }
    
    die("ok");
  }

  private static function fill($method, $url, $path){
    $params = [];

    $seperated = explode("/", $url);
    foreach ($seperated as $key => $item) {
      if(strpos($item, '{') !== false && strpos($item, '}') !== false){
        $params[] = [
          "index"       => $key,
          "replacable"  => str_replace("{", "", str_replace("}", "", $item))
        ];
      }
    }

    self::$routes["GET"]["/" . $url] = [
        "path"    => $path,
        "params"  => $params
    ];
  }

  public static function get($url = NULL, $path = NULL){
    if(!$url || !$path){
      exit("Wrong usage of Route::get() method");
    }
    self::fill("GET", $url, $path);
  }

  private static function check_existence($path){
    $path = str_replace("/", DIRECTORY_SEPARATOR, $path);

    if(!file_exists(MODULEPATH . "controllers" . DIRECTORY_SEPARATOR . $path . ".php")){

      if(strpos("/", $path)){
        exit("Couldn't find $path.php");
      }

      if(!file_exists(MODULEPATH . "controllers" . DIRECTORY_SEPARATOR . dirname($path) . ".php")){
        exit("Couldn't find ". dirname($path));
      }

      require_once MODULEPATH . "controllers" . DIRECTORY_SEPARATOR . dirname($path) . ".php";
      $className  = ucfirst(basename(dirname($path)));
      $methodName = basename($path);

      if(!class_exists($className)){
        exit("Couldn't find class $className");
      }

      $class     = new $className;

      if(!method_exists($class, $methodName)){
        exit("Class $className doesn't have method '$methodName'");
      }
    }
  }
}
