<?php $action = isset($action) ? $action : 'show_all'; ?>

<div class="span-24">
<?php include('admin_menu.php');?>
<h2>
cPage - 
<?php 
echo ucfirst(str_replace('_', ' ' , strtolower($action))); 
echo !empty($action_item) ? ' article <strong>' . $action_item . '</strong>' : '';
?>
</h2>
<?php switch ($action) {
case 'edit':
case 'add':
	include('cpage_add.php');
	break;
case 'delete':
case 'activate':
case 'deactivate':
case 'show_all':
default:
	include('cpage_all.php');
	break;
} ?>
</div>
