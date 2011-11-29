<?php

require_once('system/core/okapi.php');

define("BASE_PATH", (dirname(__FILE__) . '/'));

function dump($item, $die=true)
{
	$printString = '<pre>' . print_r($item, true) . '</pre>';
	if($die)
		die($printString);
	else
		echo $printString;
}

// dump($_GET);

$okapi = new Okapi();


$okapi->dispatch();

