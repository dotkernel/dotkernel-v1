<span style="color: #ff0000;">{ERROR}</span>
<br />
<form action="{SITE_URL}/admin/admin/add" method="post" >
<input type="hidden" name="send" value="on">
	<table cellpadding="0" cellspacing="1" class="big_table" width="30%">
		<tr>
			<td class="row2"><span >Username:</span></td>
			<td class="row1"><input type="text" name="username" value="{USERNAME}" /></td>
		</tr>
		<tr>
			<td class="row2"><span >Password:</span></td>
			<td class="row1"><input type="password" name="password" value="{PASSWORD}" /></td>
		</tr>
		<tr>
			<td class="row2"><span >Re-type Password:</span></td>
			<td class="row1"><input type="password" name="password2" value="{PASSWORD}" /></td>
		</tr>		
		<tr>
			<td class="row2"><span >Email:</span></td>
			<td class="row1"><input type="text" name="email" value="{EMAIL}" /></td>
		</tr>			
		<tr>
			<td class="row2"><span >First Name:</span></td>
			<td class="row1"><input type="text" name="firstName" value="{FIRSTNAME}" /></td>
		</tr>		
		<tr>
			<td class="row2"><span >Last Name:</span></td>
			<td class="row1"><input type="text" name="lastName" value="{LASTNAME}" /></td>
		</tr>
		<tr>
			<td colspan="2" class="row1" align="center"><input type="submit" onclick="" class="small_btn" value="add"></td>
		</tr>
	</table>
</form>