</div> <!-- content span-24 wrapper div -->
<footer class="okapi-footer span-24">
<p>This is the footer, you might want to put som contact information here... Or something else.. I don't know</p>

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
