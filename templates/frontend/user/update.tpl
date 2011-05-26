<form action="{SITE_URL}/user/account/" method="post">
<input type="hidden" name="userToken" value="{USERTOKEN}">
	<table class="form">
		<tr>
			<td><label>Username:</label></td>
			<td style="line-height:36px"><strong>{USERNAME}</strong></td>
		</tr>
		<tr>
			<td><label for="password">Password:</label></td>
			<td><input type="password" name="password" value="{PASSWORD}" id="password" /></td>
		</tr>
		<tr>
			<td><label for="password2">Re-Type Password:</label></td>
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
			<td></td>
			<td><input type="submit" class="button" value="Update" /></td>
		</tr>
	</table>
</form>