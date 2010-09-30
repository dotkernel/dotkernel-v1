<form action="{SITE_URL}/admin/user/delete/id/{ID}" method="post" >
<input type="hidden" name="send" value="on">
<fieldset style="width: 450px">
<legend>Delete User Acccount: {USERNAME}</legend>
	<table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
		<tr>
			<td class="row2"><b>Are you sure you want to delete this account ?</b></td>
			<td class="row1">
				<label for="active1">YES</label><input type="radio" id="active1" value="1" name='delete'/>
				<label for="active0">NO</label><input type="radio" id="active0" value="0" name='delete' checked /></td>
		</tr>	
		<tr>
			<td class="row2"> </td>
			<td class="row1 last_td" >
				<input type="submit" onclick="" class="small_btn" value="OK"></td>
		</tr>
	</table>
</fieldset>
</form>