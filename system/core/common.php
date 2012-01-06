<?php

function path_to($name) {
	$okapi = Okapi::singleton();
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
	unset($okapi);
}

function generate_menu($menu_items, $nav_class='okapi-nav', $active_menu_item = 'active_menu_item', $full_menu=true) {
	if (isset($menu_items) && is_array($menu_items)) {
		$class_nav = empty($nav_class) ? '' : ' class="' . $nav_class . '"';

		$active = get_active_menu_item($active_menu_item);
		echo $full_menu ?"<nav{$class_nav}>" : '';
		foreach($menu_items as $item) {
			$external = '';
			$external_icon =''; // TODO: add setting for this in config, you might not want an icon for external links...
			if (isset($item['external']) && $item['external'] == 1) {
				$external_icon = ' <img src="' . path_to('theme') . '/img/external_link_icon.png" alt="opens in new tab" />';
				$external = ' target="_BLANK"';
			}
			$title = '';
			if (isset($item['title']) && !empty($item['title']))
				$title = " title=\"{$item['title']}\"";

			if ($active != null && (strtolower($active) == strtolower($item['text']) || (stripos($item['url'], $active) != false)))
				echo "<a href=\"{$item['url']}\" class=\"active\"{$title}{$external}>{$item['text']}</a>";
			else
				echo "<a href=\"{$item['url']}\"{$title}{$external}>{$item['text']}{$external_icon}</a>";
		}
		echo $full_menu ? "</nav>" : '';
	} else {
		echo "<nav><a href=\"#\">Faulty/No menu</a></nav>";
	}

}

function set_active_menu_item($item, $key='active_menu_item', $force = false) {
	if(!isset($_REQUEST[$key]) || $force)
		$_REQUEST[$key] = $item;
}

function get_active_menu_item($key='active_menu_item') {
	$retval = '';
	// checks if a variable has already been declared, overriding the $active, ugly, I know
	if (isset($_REQUEST[$key]))
		$retval = $_REQUEST[$key];
	return $retval;
}
