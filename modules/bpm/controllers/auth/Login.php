<?php

class Login extends Controller{

  public function index(){
    echo "index function goes on";
  }


  public function action($params){
    echo $params["id"];
  }
}
