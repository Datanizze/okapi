<?php
$c_types = array('plain' => 'Plain Text', 'html' => 'HTML', 'php' => 'PHP');
$a_types = array('article' => 'Article', 'blog' => 'Blog post', 'news' => 'News');
?>
<p>* = Required fields</p>
<?php echo isset($status) ? "<span class=\"{$status['status']}\">{$status['message']}</span><br /><br />" : ''; ?>
<form action="" method="post" class="okapi-form">
<input rel="tipsy"
	type="text" 
	name="title" 
	value="<?php echo isset($article['title']) ? $article['title'] : (isset($_POST['title']) ? $_POST['title'] : ''); ?>" 
	class="title" 
	placeholder="Article Title" 
	title="Article title, this is the text that will be shown where '<strong>cPage - <?php echo ucfirst(str_replace('_', ' ' , strtolower($action))); ?></strong>' is now."/> *<br /> 

<input rel="tipsy" 
	type="text" 
	name="key" 
	value="<?php echo isset($article['key']) ? $article['key'] : (isset($_POST['key']) ? $_POST['key'] : ''); ?>" 
	placeholder="Article Key" 
	class="text"
	title="Key for accessing this article via url, leave empty to use the article's title." /> <br />
 <br />

<label for="content">Article Content:</label> * <br />
<textarea rel="elrte" name="content" class="okapi-textarea">
<?php echo isset($article['content']) ? sanitize_html($article['content']) : (isset($_POST['content']) ? $_POST['content'] : ''); ?>
</textarea>
<br />

<label for="content_type">Content type:</label>
<select rel="tipsy"
	class="text"
	name="content_type"
	title="Sets how the article will be interpreted.<br> plain text just outputs everything as verbatim while html outputs html code. <br> php will eval php code."><?php
$c_type = isset($article['content_type']) ? $article['content_type'] : (isset($_POST['content_type']) ? $_POST['content_type'] : '');
foreach($c_types as $key => $val) {
	if ($key == $c_type)
		echo "<option value=\"{$key}\" selected>{$val}</option>";
	else
		echo "<option value=\"{$key}\">{$val}</option>";
}
?>
</select> * <br />

<label for="article_type">Article Type:</label>
<select rel="tipsy"
	name="type"
	title="Type of article, Not currently used but could be in the future"
	disabled>
<?php 
$a_type = isset($article['type']) ? $article['type'] : (isset($_POST['type']) ? $_POST['type'] : '');
foreach($a_types as $key => $val) {
	if ($key == $a_type)
		echo "<option value=\"{$key}\" selected>{$val}</option>";
	else
		echo "<option value=\"{$key}\">{$val}</option>";
} ?>
</select> Not currently in use.<br />


<label for="active">Active:</label>
<input type="checkbox"
	<?php echo isset($article['active']) ? ($article['active']==1 ? 'checked="checked"' : '') : (isset($_POST['active']) ? 'checked="checked"' : ''); ?>
	name="active"
	title="Sets if this article should be listed in article lists and if it should be accessible at all."> <br />

<input type="submit" name="submit" class="okapi-button okapi-green right" value="Save Article!" />
<input type="reset" name="reset" class="okapi-button okapi-white right" value="Reset" />
</form>
