<h2>cPage - Start - All Pages</h2>

<div class="span-24">
<table class="okapi-table">
	<tr class="okapi-table-row">
		<th class="okapi-table-cell">
			Key
		</th>
		<th class="okapi-table-cell">
			Title
		</th>
		<th class="okapi-table-cell">
			Content
		</th>
		<th class="okapi-table-cell">
			Content Type
		</th>
		<th class="okapi-table-cell">
			Active
		</th>
		<th class="okapi-table-cell">
			Created
		</th>
		<th class="okapi-table-cell">
			Published
		</th>
		<th class="okapi-table-cell">
			Modified
		</th>
		<th class="okapi-table-cell">
			Author
		</th>
	</tr>
	<?php foreach($articles as $article) { ?>
		<tr class="okapi-table-row">
			<?php foreach($article as $key => $val) { ?>
			<td class="okapi-table-cell">
				<?php echo $val; ?>
			</td>
			<?php } ?>
			<td class="okapi-table-cell"><a href="#"><img src="<?php echo $tp; ?>/img/icons/document--pencil.png" title="Edit article"/></a></td>
			<td class="okapi-table-cell"><a href="#"><img src="<?php echo $tp; ?>/img/icons/minus-button.png" title="Remove article"/></a></td>
		</tr>
	<?php } ?>
</table>
</div>
