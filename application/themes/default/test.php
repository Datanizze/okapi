<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
	<title><?php echo $title; ?></title>
</head>
<body>
<h1><?php echo $h1; ?></h1>
<table border=1>
<pre>
<?php 
print_r(($db_object->fetch_fields()));
while ($row = $db_object->fetch_object()) { ?>
</pre>
<tr>
<?php foreach ($row as $key => $val) { ?>
<td><?php echo $key; ?></td>
<td><?php echo $val; ?></td>

<?php } ?>
</tr>
<?php }?>
</table>
</body>
</html>
