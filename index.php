<?php
session_start();

define("BASE_PATH", (dirname(__FILE__))); 
define("APPLICATION_PATH", BASE_PATH . '/application');
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

