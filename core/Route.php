<?php

class Route{
  private static $routes = [];
  private static $prefix = "";

  public static function action(){
    $request_method = $_SERVER["REQUEST_METHOD"];
    $request_uri    = $_SERVER["REQUEST_URI"];
    $params         = [];

    $path = self::$routes[$request_method][$request_uri]["path"] ?? "";

    if(!isset(self::$routes[$request_method][$request_uri])){
      $url_keys      = array_keys(self::$routes[$request_method]);
      $url_key_value = [];

      foreach ($url_keys as $key => $url) {
        $url_key_value[$key] = explode("/", $url);
      }

      $pieces   = explode('/', $request_uri);
      $route    = false;
      $path_key = NULL;
      $isValid  = false;

      foreach ($url_key_value as $key => $urls) {
        if(count($urls) === count($pieces)){
          $isValid = true;

          for($i = 0; $i < count($urls); $i++){
            if($urls[$i] !== $pieces[$i] && !(strpos($urls[$i], '{') !== false && strpos($urls[$i], '}') !== false)){
              $isValid = false;
              continue;
            }else if(strpos($urls[$i], '{') !== false && strpos($urls[$i], '}') !== false){
              $p_key           = str_replace("{", "", str_replace("}", "", $urls[$i]));
              $params[$p_key]  = $pieces[$i];
              $path_key        = $key;
            }else if($urls[$i] === $pieces[$i]){
              $path_key        = $key;
              continue;
            }else{
              $isValid = false;
            }
          }

          if($isValid){
            break;
          }
        }
      }

      $route = $isValid ? self::$routes[$request_method][$url_keys[$path_key]]["path"] : NULL;

      if(!$route){
        exit("Route not found");
      }

      $path = $route;
    }

    self::check_existence($path, $params);
  }

  private static function fill($method = "GET", $url = NULL, $path = NULL){
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
    $a_key = self::$prefix ? "/" . self::$prefix : "";
    self::$routes["GET"][$a_key. "/" . $url] = [
        "path"    => $path,
        "params"  => $params
    ];
  }

  public static function prefix($prefix = NULL, Closure $callback){
    if(!$prefix){
      exit("Wrong usage of prefix");
    }

    self::$prefix = self::$prefix ? self::$prefix . "/$prefix" : $prefix;

    call_user_func($callback);

    self::$prefix = "";
  }

  public static function get($url = NULL, $path = NULL, $prefix = ""){
    if(!$url || !$path){
      exit("Wrong usage of Route::get() method");
    }
    self::fill("GET", $url, $path, $prefix);
  }

  public static function post($url = NULL, $path = NULL, $prefix = ""){
    if(!$url || !$path){
      exit("Wrong usage of Route::post() method");
    }
    self::fill("POST", $url, $path, $prefix);
  }

  public static function put($url = NULL, $path = NULL, $prefix = ""){
    if(!$url || !$path){
      exit("Wrong usage of Route::put() method");
    }
    self::fill("PUT", $url, $path, $prefix);
  }

  public static function delete($url = NULL, $path = NULL, $prefix = ""){
    if(!$url || !$path){
      exit("Wrong usage of Route::delete() method");
    }
    self::fill("DELETE", $url, $path, $prefix);
  }

  private static function check_existence($path, $params){
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
    }else{
      require_once MODULEPATH . "controllers" . DIRECTORY_SEPARATOR . $path . ".php";
      $className  = ucfirst(basename($path));
      $methodName = "index";

      if(!class_exists($className)){
        exit("Couldn't find class $className");
      }

      $class     = new $className;

      if(!method_exists($class, $methodName)){
        exit("Class $className doesn't have method '$methodName'");
      }
    }

    call_user_func([$class, $methodName], $params);
  }
}
