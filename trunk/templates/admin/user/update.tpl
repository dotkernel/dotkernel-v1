<form action="{SITE_URL}/admin/user/update/id/{ID}" method="post" >
<input type="hidden" name="send" value="on">
<fieldset style="width: 500px">
<legend>Update User Account</legend>
	<table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
		<tr>
			<td class="row2"><label>Username</label><input type="text" name="username" value="{USERNAME}" class="medium"></td>
		</tr>
		<tr>
			<td class="row1"><label>Password</label><input type="password" name="password" value="{PASSWORD}" class="medium"> </td>
		</tr>
		<tr>
			<td class="row2"><label>Re-type Password</label><input type="password" name="password2" value="{PASSWORD}"  class="medium"></td>
		</tr>		
		<tr>
			<td class="row1"><label>Email</label><input type="text" name="email" value="{EMAIL}" class="medium" ></td>
		</tr>			
		<tr>
			<td class="row2"><label>First Name</label><input type="text" name="firstName" value="{FIRSTNAME}"  class="medium"></td>
		</tr>		
		<tr>
			<td class="row1"><label>Last Name</label><input type="text" name="lastName" value="{LASTNAME}"  class="medium"></td>
		</tr>		
		<tr>
			<td class="row2"><label>Active</label>
				<span>Yes</span> <input type="radio" name="isActive" value="1"  {ACTIVE_1}> 
				<span>No</span><input type="radio" name="isActive" value="0"  {ACTIVE_0}>
			</td>
		</tr>
		<tr>
			<td  class="row1"  style="text-align: center;">
				<input type="submit" onclick="" class="small_btn" value="add"></td>
		</tr>
	</table>
</fieldset>
</form>