<?php
defined("BASEPATH") OR exit("No direct scripts allowed");

  Route::prefix("{id}",function(){
    Route::get("login", "auth/Login/action");
  });
