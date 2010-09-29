<form action="{SITE_URL}/admin/admin/add" method="post" >
<input type="hidden" name="send" value="on">
<fieldset style="width: 450px">
<legend>Add New Admin</legend>
	<table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
		<tr>
			<td class="table_subhead"><label>Username</label><input type="text" name="username" value="{USERNAME}" class="medium"></td>
		</tr>
		<tr>
			<td><label>Password</label><input type="password" name="password" value="{PASSWORD}" class="medium"> </td>
		</tr>
		<tr>
			<td><label>Confirm Password</label><input type="password" name="password2" value="{PASSWORD}"  class="medium"></td>
		</tr>		
		<tr>
			<td><label>Email</label><input type="text" name="email" value="{EMAIL}" class="medium" ></td>
		</tr>			
		<tr>
			<td><label>First Name</label><input type="text" name="firstName" value="{FIRSTNAME}"  class="medium"></td>
		</tr>		
		<tr>
			<td><label>Last Name</label><input type="text" name="lastName" value="{LASTNAME}"  class="medium"></td>
		</tr>		
		<tr>
			<td><label>Active</label>
			<label for="isactive">Yes </label><input type="radio" name="isActive" value="1"  {ACTIVE_1} id="isactive"/>
			<label for="noactive">No </label><input type="radio" name="isActive" value="0"  {ACTIVE_0} id="noactive"/>
			</td>
		</tr>
		<tr>
			<td class="button_area">
				<input type="submit" onclick="" class="small_btn" value="add"></td>
		</tr>
	</table>
</fieldset>
</form>