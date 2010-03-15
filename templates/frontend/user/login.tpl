<span style="color: #ff0000;">{ERROR}</span>
	<p class="left_head">Login</p>
	<div class="login">
		<form action="{SITE_URL}/user/authorize" method="post" >
			<input type="hidden" name="send" value="on">
			<p>Username</p><input type="text" name="username" />
			<p>Password</p><input type="password" name="password" />
			<input type="submit" onclick="" class="btn" value="login">
		</form>
		<a href="{SITE_URL}/user/forgot-password">Password Recovery</a>
	</div>