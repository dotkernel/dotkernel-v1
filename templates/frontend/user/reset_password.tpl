<span style="color: #ff0000;">{ERROR}</span>
<br>
<p><strong>Strong passwords include numbers, letters, and punctuation marks.</strong></p>

<form action="{SITE_URL}/user/reset-password" method="post" >
<input type="hidden" name="userToken" value="{USERTOKEN}">
<input type="hidden" name="userId" value="{USERID}">
	<ul class="form">
		<li class="clearfix">
			<label for="password">Password</label>
			<input id="password" type="password" name="password" />
		</li>
		<li class="clearfix">
			<label for="password2">Retype Password</label>
			<input id="password2" type="password" name="password2" />
		</li>
		<li class="clearfix">
			<label class="empty">&nbsp;</label>
			<input type="{DISABLED}" class="button" value="Submit" />
		</li>
	</ul>
</form>