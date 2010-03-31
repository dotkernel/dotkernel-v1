<span style="color: #ff0000;">{ERROR}</span>
<br />
<form action="{SITE_URL}/user/account/id/{ID}" method="post">
<input type="hidden" name="send" value="on">
<input type="hidden" name="id" value="{ID}">
<ul class="g_form">
		<li class="clearfix positioned">
			<p class="contact_label">Username:</p>{USERNAME}
		</li>
		<li class="clearfix positioned">
			<p class="contact_label">Password:</p>
			<input type="password" name="password" value="{PASSWORD}" id="password" />
		</li>	
		<li class="clearfix positioned">
			<p class="contact_label">Re-type Password:</p>
			<input type="password" name="password2" value="{PASSWORD}" id="password2" />
		</li>
		<li class="clearfix positioned">
			<p class="contact_label">Email:</p>
			<input id="email" type="text" name="email" value="{EMAIL}" />
		</li>
		<li class="clearfix positioned">
			<p class="contact_label">First Name:</p>
			<input type="text" name="firstName" value="{FIRSTNAME}" id="firstName" />
		</li>
		<li class="clearfix positioned">
			<p class="contact_label">Last Name:</p>
			<input type="text" name="lastName" value="{LASTNAME}" id="lastName" />
		</li>
		<li class="clearfix positioned">
			<p class="contact_label">&nbsp;</p>
			<input type="submit" class="btn" value="Update" />
		</li>
	</ul>
</form>