<form action="{SITE_URL}/admin/user/add" method="post" >
<input type="hidden" name="userToken" value="{USERTOKEN}">
<fieldset style="width: 500px">
<legend>Add New User</legend>
	<table class="medium_table" width="100%">
		<tr>
			<td width="140px"><b>Username</b><span class="required">*</span></td>
			<td><input type="text" name="username" value="{USERNAME}"></td>
		</tr>
		<tr>
			<td><b>Password</b><span class="required">*</span></td>
			<td><input type="password" name="password" value="{PASSWORD}" ></td>
		</tr>
		<tr>
			<td><b>Confirm Password</b><span class="required">*</span></td>
			<td><input type="password" name="password2" value="{PASSWORD}" ></td>
		</tr>
		<tr>
			<td><b>Email</b><span class="required">*</span></td>
			<td><input type="text" name="email" value="{EMAIL}" ></td>
		</tr>
		<tr>
			<td><b>First Name</b></td>
			<td><input type="text" name="firstName" value="{FIRSTNAME}" ></td>
		</tr>
		<tr>
			<td><b>Last Name</b></td>
			<td><input type="text" name="lastName" value="{LASTNAME}" ></td>
		</tr>
		<tr>
			<td><b>Active</b></td>
			<td>
				<label for="active1">Yes</label> <input type="radio" id="active1" name="isActive" value="1" style="height: auto;" {ACTIVE_1}>
				<label for="active0">No</label>	<input type="radio" id="active0" name="isActive" value="0" style="height: auto;" {ACTIVE_0}>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" class="button" value="add">
			</td>
		</tr>
	</table>
</fieldset>
</form>
