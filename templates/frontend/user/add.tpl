<script>
	var SITE_URL = '{SITE_URL}';
</script>
<script type="text/javascript" src="{TEMPLATES_URL}/js/frontend/user.js"></script>
<span style="color: #ff0000;" id="msgError">{ERROR}</span>
<br/>
<form id="userRegister" action="" method="post">
<input type="hidden" name="send" value="on">

<ul class="g_form">
		<li class="clearfix positioned">
			<p class="contact_label">Username:</p>
			<input id="username" type="text" name="username" value="{USERNAME}" />
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
			<p class="contact_label">Secure Image:</p>
			{SECUREIMAGE}<div id="secure_image"></div>
		</li>
		<li class="clearfix positioned">
			<p class="contact_label">&nbsp;</p>
			<input type="submit" class="btn" value="register">
		</li>
	</ul>
</form>