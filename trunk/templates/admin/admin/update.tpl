<div class="message_{MESSAGE_TYPE}">{MESSAGE_TEXT}</div>
<form action="{SITE_URL}/admin/admin/update/id/{ID}" method="post" >
<input type="hidden" name="send" value="on">
<fieldset style="width: 500px">
<legend>My Acccount</legend>
	<table cellpadding="0" cellspacing="1" class="big_table">
		<tr>
			<td class="row2" width="130px"><b>Username</b></td>
			<td class="row1">{USERNAME}</td>
		</tr>
		<tr>
			<td class="row2"><b>Password</b></td>
			<td class="row1"><input type="password" name="password" value="{PASSWORD}" class="bigger"></td>
		</tr>
		<tr>
			<td class="row2"><b>Re-type Password</b></td>
			<td class="row1"><input type="password" name="password2" value="{PASSWORD}" class="bigger"></td>
		</tr>		
		<tr>
			<td class="row2"><b>Email</b></td>
			<td class="row1"><input type="text" name="email" value="{EMAIL}" class="bigger"></td>
		</tr>			
		<tr>
			<td class="row2"><b>First Name</b></td>
			<td class="row1"><input type="text" name="firstName" value="{FIRSTNAME}" class="bigger"></td>
		</tr>		
		<tr>
			<td class="row2"><b>Last Name</b></td>
			<td class="row1"><input type="text" name="lastName" value="{LASTNAME}" class="bigger"></td>
		</tr>
		<tr>
			<td colspan="2" class="row1" style="text-align: center;">
				<input type="submit" onclick="" class="small_btn" value="update"></td>
		</tr>
	</table>
</fieldset>
</form>