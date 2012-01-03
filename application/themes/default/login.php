<h2>Login</h2>
<form action="login" method="post" class="okapi-form">
	<?php echo !empty($login_status) ? $login_status : ''; ?>
<input class="title" type="text" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" placeholder="Your Username"/><?php echo isset($_POST['submit']) && empty($_POST['username']) ? ' <span class="error">Username can\'t be empty.</span>' : ''; ?><br />
	<input class="title" type="password" name="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>" placeholder="Your Password"/><?php echo isset($_POST['submit']) && empty($_POST['password']) ? ' <span class="error">Password can\'t be empty.</span>' : ''; ?><br />
	<input type="submit" value="Login" name="submit" />
	<br />
	<small>Lost your data? <em>Too bad!</em></small><br />
	<small>New User? <em>Don't care!</em></small><br />
	<small>How do I get in? <em>You don't...</em></small>

</form>
