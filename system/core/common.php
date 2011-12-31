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

function generate_menu($menu_items, $active=null, $nav_class='okapi-nav') {
	if (isset($menu_items) && is_array($menu_items)) {
		$class_nav = empty($nav_class) ? '' : ' class="' . $nav_class . '"';

		echo "<nav{$class_nav}>";
		foreach($menu_items as $item) {
			$title = '';
			if (isset($item['title']) && !empty($item['title']))
				$title = " title=\"{$item['title']}\"";

			if ($active != null && strtolower($active) == strtolower($item['text']))
				echo "<a href=\"{$item['url']}\" class=\"active\"{$title}>{$item['text']}</a>";
			else
				echo "<a href=\"{$item['url']}\"{$title}>{$item['text']}</a>";
		}
		echo "</ul>";
	} else {
		echo "<nav><a href=\"#\">Faulty/No menu</a></nav>";
	}
}
