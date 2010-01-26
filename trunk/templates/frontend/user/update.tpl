<span style="color: #ff0000;">{ERROR}</span>
<br />
<form action="{SITE_URL}/user/account/id/{ID}" method="post">
<input type="hidden" name="send" value="on">
<input type="hidden" name="id" value="{ID}">
<table cellpadding="0" cellspacing="1" width="40%">
	<tr>
		<td><span >Username:</span></td>
		<td>{USERNAME}</td>
	</tr>
	<tr>
		<td><span >Password:</span></td>
		<td><input type="password" name="password" value="{PASSWORD}" /></td>
	</tr>
	<tr>
		<td><span >Re-type Password:</span></td>
		<td><input type="password" name="password2" value="{PASSWORD}" /></td>
	</tr>		
	<tr>
		<td><span >Email:</span></td>
		<td><input type="text" name="email" value="{EMAIL}" /></td>
	</tr>			
	<tr>
		<td><span >First Name:</span></td>
		<td><input type="text" name="firstname" value="{FIRSTNAME}" /></td>
	</tr>		
	<tr>
		<td><span >Last Name:</span></td>
		<td><input type="text" name="lastname" value="{LASTNAME}" /></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" onclick="" class="btn" value="Update"></td>
	</tr>
</table>
</form>