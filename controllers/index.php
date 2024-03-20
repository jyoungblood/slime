<?php

// date_default_timezone_set('America/Chicago');
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

use Slime\render;



$app->get('/', function ($req, $res, $args) {

  return render::hbs($req, $res, [
    'layout' => '_layouts/base',
    'template' => 'index',
    'title' => $_ENV['site_title'],
  ]);

});




?>