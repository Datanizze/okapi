<div class="span-24">

	<?php if (!isset($articles)) { ?>
	<p>No articles found.. Create one?</p>
	<?php } else { ?>
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
				C. Type
			</th>
			<th class="okapi-table-cell">
				Active
			</th>
			<th class="okapi-table-cell">
				Created
			</th>
			<th class="okapi-table-cell">
				Author
			</th>
		</tr>
	<?php foreach($articles as $article) { ?>
		<tr class="okapi-table-row">
			<td class="okapi-table-cell">
				<a rel="tipsy" href="/page/<?php echo $article['key']; ?>" title="View article"><?php echo $article['key']; ?></a>
			</td>
			<td class="okapi-table-cell">
				<?php echo $article['title']; ?>
			</td>
			<td class="okapi-table-cell">
				<?php echo strlen($article['content']) >60 ? substr($article['content'],0,57) . '<a href="/page/' . $article['key'] . '" rel="tipsy" title="View the rest of this article">...</a>' : $article['content']; ?>
			</td>
			<td class="okapi-table-cell">
				<?php echo $article['content_type']; ?>
			</td>
			<td class="okapi-table-cell">
				<?php echo $article['active']; ?>
				<a href="/admin/activate/<?php echo $article['key']; ?>"><img rel="tipsy" src="<?php echo $tp; ?>/img/icons/dot-red.png" title="Activate article"/></a>
				<a href="/admin/deactivate/<?php echo $article['key']; ?>"><img rel="tipsy" src="<?php echo $tp; ?>/img/icons/dot-green.png" title="Deactivate article"/></a>
			</td>
			<td class="okapi-table-cell">
				<?php echo $article['created']; ?>
			</td>
			<td class="okapi-table-cell">
				<?php echo $article['author']; ?>
			</td>
			<td class="okapi-table-cell"><a href="/admin/edit/<?php echo $article['key']; ?>"><img rel="tipsy" src="<?php echo $tp; ?>/img/icons/document--pencil.png" title="Edit article"/></a></td>
			<td class="okapi-table-cell"><a href="/admin/remove/<?php echo $article['key']; ?>"><img rel="tipsy" src="<?php echo $tp; ?>/img/icons/minus-button.png" title="Remove article"/></a></td>
		</tr>
	<?php }} ?>
</table>
</div>