<?php

function path_to($name) {
	global $okapi;
	$paths = array();
	$paths['theme'] = '/application/themes/' . $okapi->config['theme'];
	$paths['core'] = '/system/core';
	switch ($name) {
	case 'theme':
	case 'core':
		return $paths[$name];
		break;
	default:
		return null;
	}
}
