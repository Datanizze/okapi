<?php $action = isset($action) ? $action : 'show_all'; ?>
<h2>
cPage - 
<?php 
echo ucfirst(str_replace('_', ' ' , strtolower($action))); 
echo !empty($action_item) ? ' article <strong>' . $action_item . '</strong>' : '';
?>
</h2>
<?php
switch ($action) {
case 'edit':
	include('cpage_edit.php');
	break;
case 'add':
	include('cpage_add.php');
	break;
case 'delete':
	include('cpage_del.php');
	break;
case 'activate':
case 'deactivate':
	include('cpage_activate.php');
case 'show_all':
default:
	include('cpage_all.php');
	break;
}
