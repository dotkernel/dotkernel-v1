<script>
	var SITE_URL = '{SITE_URL}';
</script>
<script type="text/javascript" src="{TEMPLATES_URL}/js/frontend/user.js"></script>
<div class="message_error" style="display:none" id="msgError"></div>
<br/>
<form id="userRegister" action="" method="post">
	<table class="form">
		<tr>
			<td><label for="username">Username:</label></td>
			<td><input id="username" type="text" value="{USERNAME}" name="username"></td>
		</tr>
		<tr>
			<td><label for="password">Password:</label></td>
			<td><input type="password" name="password" value="{PASSWORD}" id="password" /></td>
		</tr>
		<tr>
			<td><label for="password2">Re-type Password:</label></td>
			<td><input type="password" name="password2" value="{PASSWORD}" id="password2" /></td>
		</tr>
		<tr>
			<td><label for="email">Email:</label></td>
			<td><input id="email" type="text" name="email" value="{EMAIL}" /></td>
		</tr>
		<tr>
			<td><label for="firstName">First Name:</label></td>
			<td><input type="text" name="firstName" value="{FIRSTNAME}" id="firstName" /></td>
		</tr>
		<tr>
			<td><label for="lastName">Last Name:</label></td>
			<td><input type="text" name="lastName" value="{LASTNAME}" id="lastName" /></td>
		</tr>
		<tr>
			<td><label>Secure Image:</label></td>
			<td>{SECUREIMAGE}<div id="secure_image"></div></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" class="button" value="Register"></td>
		</tr>
	</table>
</form>