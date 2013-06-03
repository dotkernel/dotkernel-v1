<span style="color: #ff0000;">{ERROR}</span>

<form action="{SITE_URL}/user/authorize" method="post" >
	<table class="form">
		<tr>
			<td><label for="username">Username:</label></td>
			<td><input id="username" type="text" value="{USERNAME}" name="username"></td>
		</tr>
		<tr>
			<td><label for="password">Password:</label></td>
			<td><input id="password" type="password" value="{PASSWORD}" name="password"></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" onclick="" class="button" value="Log In"></td>
		</tr>
		<tr>
			<td></td>
			<td><a href="{SITE_URL}/user/forgot-password" style="font-size:smaller">Password Recovery</a> </td>
		</tr>
	</table>
</form>