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

// simply tries to include header.php if found in the theme's directory
function get_header() {
	if (file_exists(BASE_PATH . path_to($theme) . '/header.php'))
		include(BASE_PATH . path_to($theme) . '/header.php');
	else
		echo '<p>get_header() failed, no header.php found in the theme\'s base directory</p>';
}

// same as get_header but for footer.php
function get_footer() {
	if (file_exists(BASE_PATH . path_to($theme) . '/footer.php'))
		include(BASE_PATH . path_to($theme) . '/footer.php');
	else
		echo '<p>get_footer() failed, no footer.php found in the theme\'s base directory</p>';
}
