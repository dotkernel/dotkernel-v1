<form action="{SITE_URL}/admin/user/delete/id/{ID}" method="post" >
<input type="hidden" name="send" value="on">
<input type="hidden" name="userToken" value="{USERTOKEN}">
<fieldset style="width: 540px">
<legend>Delete User Acccount: {USERNAME}</legend>
	<table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
		<tr>
			<td>
				<b>Are you sure you want to delete this account ?</b>
				<br />
				<input type="checkbox" name="confirm">Confirm deletion</td>
			<td style="vertical-align: middle;">
				<input type="submit" class="small_btn" value="YES" style="float: left; margin-right:10px;">
				<input type="button" onclick="window.location = '{SITE_URL}/admin/user/list'" class="small_btn" value="Cancel">
			</td>
		</tr>
	</table>
</fieldset>
</form>