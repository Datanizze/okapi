<?php $tp = path_to('theme'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title><?php echo isset($title) ? $title : 'Page Title Not Set';?></title>
	<link rel="stylesheet" href="<?php echo $tp; ?>/css/blueprint/screen.css" type="text/css" media="screen, projection" />  
	<link rel="stylesheet" href="<?php echo $tp; ?>/css/blueprint/print.css" type="text/css" media="print" />  
	<link rel="stylesheet" href="<?php echo $tp; ?>/css/okapi_default.css" type="text/css" media="screen, projection" />
	<!--[if IE]><link rel="stylesheet" href="<?php echo $tp; ?>/css/blueprint/ie.css" type="text/css" media="screen, projection" /><![endif]-->
</head>
<body>
<div id="container">
	<div class="span-24"> <!-- header div -->
		<h1><?php echo isset($header_title) ? $header_title : 'Header Title';?></h1>
	</div> <!-- header div -->
	<div class="span-24"> <!-- menu div -->
		<?php 
			@generate_menu($menu, 'about');
		?>
	</div> <!-- menu div -->
</div>
