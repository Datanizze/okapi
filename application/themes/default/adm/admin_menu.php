<?php 
$menu_items = array(
	array('text' => 'Control Pages', 'url' => '/cms/admin/cpage/', 'title' => 'Administrate all your articles/pages'),
	array('text' => 'Canonical Urls', 'url' => '/cms/admin/canurl/', 'title' => ''),
	array('text' => 'Main Menu', 'url' => '/cms/admin/main_menu/', 'title' => 'Edit the main menu')
);
?>

<?php generate_menu($menu_items, 'okapi-subnav', 'active_submenu_item'); ?>
