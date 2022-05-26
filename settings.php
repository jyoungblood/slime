<?php

$GLOBALS['site_title'] = 'Tabula Rasa';
$GLOBALS['site_code'] = 'TR';
$GLOBALS['site_domain'] = 'example.com';

$GLOBALS['locals'] = [
  'year' => date('Y'),
  'site_title' => $GLOBALS['site_title'],
  'site_code' => $GLOBALS['site_code'],
  'site_domain' => $GLOBALS['site_domain'],
];

// $GLOBALS['settings']['database'] = [
//   'host' => 'localhost',
//   'name' => 'db_name',
//   'user' => 'db_user',
//   'password' => 'db_pw'
// ];

// $GLOBALS['settings']['mailgun'] = [
//   'api_key' => 'key-0000000000000',
//   'domain' => 'notifications.example.com'
// ];

// date_default_timezone_set('America/Chicago');

// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

?>