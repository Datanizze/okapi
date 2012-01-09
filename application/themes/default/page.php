<div class="okapi-article">
	<h2><?php echo isset($article['title']) ? $article['title'] : 'Untitled Article'; ?></h2>

	<?php echo $article['active']==0 ? '<p><span class="notice">This article is deactivated and will only show for logged in users.</span></p>' : ''; ?>

	<?php 
	$article['content'] = isset($article['content']) ? $article['content'] : 'No content!';

	switch($article['content_type']) {
	case 'plain':
		echo nl2br(sanitize_html($article['content']));
		break;
	case 'php':
		eval('?>' . $article['content']);
		break;
	case 'html':
	default:
		echo $article['content'];
		break;
	} ?>
	<hr class="okapi-hr" />

	<small class="right">
	<?php echo isset($article['created']) ? $article['created'] : '0000-00-00 00:00:00'; ?>
	</small>
</div>
