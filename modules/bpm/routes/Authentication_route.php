<?php
defined("BASEPATH") OR exit("No direct scripts allowed");

Route::get("{id}/login", "auth/login/action");
Route::get("index", "auth/login/action");
