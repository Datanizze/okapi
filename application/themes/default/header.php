<?php 
$tp = path_to('theme'); 
$root = path_to('root'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo isset($site['title']) ? $site['title'] : 'Page Title Not Set';?></title>
	<meta charset="UTF-8" />
	<?php
	if (isset($site) && isset($site['meta'])) {
		foreach ($site['meta'] as $title => $content) {
			echo "<meta title=\"{$title}\" content=\"{$content}\">\n\t";
		}
	}
	?>

	<link rel="stylesheet" href="<?php echo $tp; ?>/css/blueprint/screen.css" type="text/css" media="screen, projection" />  
	<link rel="stylesheet" href="<?php echo $tp; ?>/css/blueprint/print.css" type="text/css" media="print" />  
	<!--[if IE]><link rel="stylesheet" href="<?php echo $tp; ?>/css/blueprint/ie.css" type="text/css" media="screen, projection" /><![endif]-->
	<link rel="stylesheet" href="<?php echo $tp; ?>/css/tipsy.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="<?php echo $tp; ?>/css/jqueryui/smoothness/jquery-ui-1.8.16.custom.css" type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="<?php echo $tp; ?>/css/elrte.min.css" type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="<?php echo $tp; ?>/css/okapi_default.css" type="text/css" media="screen, projection" />

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
	<script src="<?php echo $tp; ?>/js/elrte.min.js" ></script>
	<script src="<?php echo $tp; ?>/js/jquery.tipsy.js"></script>

	<?php
	if (isset($site) && isset($site['js'])) {
		foreach ($site['js'] as $src) {
			echo "<script src=\"{$src}\"></script>\n\t";
		}
	}
	?>

	<?php
	if (isset($site) && isset($site['css'])) {
		foreach ($site['css'] as $src) {
			echo "<link rel=\"stylesheet\" href=\"{$src}\" type=\"text/css\" media=\"screen, projection\" />\n\t";
		}
	}
	?>
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
</head>
<body>
<?php 
$authed = isset($authed) ? $authed : 'que?';
echo "<div class=\"abs_top_right\">{$authed}</div>"; ?>
<div id="okapi-wrapper"> <!-- wrapper div -->
	<div class="span-24"> <!-- header div -->
		<?php echo isset($site['header']) ? $site['header'] : '<h1>Header Title</h1>';?>
	</div> <!-- header div -->
	<div class="span-24 okapi-menu-wrapper"><!-- menu div -->
		<?php 
			generate_menu($menu);
		?>
	</div> <!-- menu div -->
	<div id="okapi-main-content" class="span-24"> <!-- content span-24 wrapper div -->
