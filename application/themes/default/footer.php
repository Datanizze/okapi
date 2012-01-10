</div> <!-- content span-24 wrapper div -->
<footer class="okapi-footer span-24">
<p><?php echo $site['footer_text'] ?></p>

<small class="quiet">&copy; Copyright 
<?php echo date('Y'); ?> 
<?php 
echo !empty($site['copyright_holder']) ? $site['copyright_holder'] : 'Copyright Holder';
echo ', ';
echo !empty($site['copyright_notice']) ? $site['copyright_notice'] : 'Copyright Notice';
?>
</small>
</footer>
</div> <!-- wrapper div -->

<script>
$(function() {
	$('img[rel=tipsy]').tipsy({gravity: 's'});
	$('a[rel=tipsy]').tipsy({gravity: 's'});
	$('label[rel=tipsy]').tipsy({gravity: 'w'});
	$('.okapi-nav a').tipsy({gravity: 's'});
	$('.okapi-subnav a').tipsy({gravity: 'nw'});
	$('.okapi-form input[rel=tipsy]').tipsy({html: true, trigger: 'focus', gravity: 'w'});
	$('.okapi-form input[type=checkbox]').tipsy({html: true, gravity: 'w'});
	$('.okapi-form select[rel=tipsy]').tipsy({html: true, gravity: 'w'});

	/*$('textarea[rel=elrte]').elrte({toolbar: 'maxi'});*/
});
</script>
</body>
</html>
