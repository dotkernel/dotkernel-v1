<form action="{SITE_URL}/admin/admin/update/id/{ID}" method="post" >
<input type="hidden" name="send" value="on">
<fieldset style="width: 450px">
<legend>Admin Acccount</legend>
	<table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
		<tr>
			<td class="row2" width="120px"><b>Username</b></td>
			<td class="row1"><input type="text" name="username" value="{USERNAME}"></td>
		</tr>
		<tr>
			<td class="row2"><b>Password</b></td>
			<td class="row1"><input type="password" name="password" value="{PASSWORD}" ></td>
		</tr>
		<tr>
			<td class="row2"><b>Confirm Password</b></td>
			<td class="row1"><input type="password" name="password2" value="{PASSWORD}" ></td>
		</tr>		
		<tr>
			<td class="row2"><b>Email</b></td>
			<td class="row1"><input type="text" name="email" value="{EMAIL}" ></td>
		</tr>			
		<tr>
			<td class="row2"><b>First Name</b></td>
			<td class="row1"><input type="text" name="firstName" value="{FIRSTNAME}" ></td>
		</tr>		
		<tr>
			<td class="row2"><b>Last Name</b></td>
			<td class="row1"><input type="text" name="lastName" value="{LASTNAME}" ></td>
		</tr>		
		<tr>
			<td class="row2"><b>Active</b></td>
			<td class="row1">
				<label for="active1">Yes</label> <input type="radio" id="active1" name="isActive" value="1" style="height: auto;" {ACTIVE_1}> 
				<label for="active0">No</label>	<input type="radio" id="active0" name="isActive" value="0" style="height: auto;" {ACTIVE_0}>
			</td>
		</tr>
		<tr>
			<td class="row2"> </td>
			<td class="row1 last_td" >
				<input type="submit" onclick="" class="small_btn" value="add"></td>
		</tr>
	</table>
</fieldset>
</form>