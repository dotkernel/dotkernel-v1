<span style="color: #ff0000;">{ERROR}</span>

<form action="{SITE_URL}/user/authorize" method="post" >
	<ul class="form">
		<li class="clearfix">
			<label for="username">Username:</label>
			<input id="username" type="text" value="{USERNAME}" name="username">
		</li>
		<li class="clearfix">
			<label for="password">Password:</label>
			<input id="password" type="password" value="{PASSWORD}" name="password">
		</li>
		<li class="clearfix">
			<label class="empty">&nbsp;</label>
			<input type="submit" onclick="" class="button" value="Log In">
		</li>
		<li class="clearfix">
			<label class="empty">&nbsp;</label>
			<a href="{SITE_URL}/user/forgot-password" style="font-size:smaller">Password Recovery</a>
		</li>
	</ul>
</form>