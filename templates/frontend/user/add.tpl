<script type="text/javascript" src="{TEMPLATES_URL}/js/frontend/user.js"></script>
<span style="color: #ff0000;" id="msgError">{ERROR}</span>
<br />
<form id="userRegister" action="" method="post">
<input type="hidden" name="send" value="on">
<table cellpadding="0" cellspacing="1" width="60%">
	<tr>
		<td><span >Username:</span></td>
		<td><input id="username" type="text" name="username" value="{USERNAME}" /></td>
	</tr>
	<tr>
		<td><span >Password:</span></td>
		<td><input type="password" name="password" value="{PASSWORD}" id="password" /></td>
	</tr>
	<tr>
		<td><span >Re-type Password:</span></td>
		<td><input type="password" name="password2" value="{PASSWORD}" id="password2" /></td>
	</tr>		
	<tr>
		<td><span >Email:</span></td>
		<td><input id="email" type="text" name="email" value="{EMAIL}" /></td>
	</tr>			
	<tr>
		<td><span >First Name:</span></td>
		<td><input type="text" name="firstName" value="{FIRSTNAME}" id="firstName" /></td>
	</tr>		
	<tr>
		<td><span >Last Name:</span></td>
		<td><input type="text" name="lastName" value="{LASTNAME}" id="lastName" /></td>
	</tr>			
	<tr>
		<td><span >Secure Image:</span></td>
		<td>{SECUREIMAGE}<div id="secure_image"></div></td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<input type="button" class="btn" value="register" onclick="formSubmit('userRegister', 'msgError', '{SITE_URL}/user/account');">
		</td>
	</tr>
</table>
</form>