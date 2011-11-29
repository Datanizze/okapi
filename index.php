<?php

define("BASE_PATH", (dirname(__FILE__) . '/'));

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

$okapi->test = 'kaka';
$okapi->dispatch();

