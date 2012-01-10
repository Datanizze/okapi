<?php
session_start();

date_default_timezone_set('Europe/Stockholm');

define("BASE_PATH", (dirname(__FILE__))); 
$root = dirname($_SERVER['PHP_SELF']);
$root = $root != '/' ? $root.'/' : $root;
define("URL_ROOT", $root);
define("APPLICATION_PATH", BASE_PATH . '/application');
define("ENVIRONMENT", 'dev');

if (ENVIRONMENT === 'dev') {
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
}
require_once(BASE_PATH . '/system/core/okapi.php');

function dump($item, $die=true)
{
	$printString = '<pre>' . print_r($item, true) . '</pre>';
	if($die)
		die($printString);
	else
		echo $printString;
}

// dump($_GET);

$okapi = Okapi::singleton();
$okapi->dispatch();

