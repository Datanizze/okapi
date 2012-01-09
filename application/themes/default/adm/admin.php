<?php $action = isset($action) ? $action : 'start'; ?>

<div class="span-24">
	<?php include('admin_menu.php'); ?>
	<h2>
	Admin -
	<?php 
	echo ucfirst(str_replace('_', ' ' , strtolower($action))); 
	echo !empty($action_item) ? ' article <strong>' . $action_item . '</strong>' : '';
	?>
	</h2>
</div>
