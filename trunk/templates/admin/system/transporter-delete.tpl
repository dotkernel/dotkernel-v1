<form action="{SITE_URL}/admin/system/transporter-delete/id/{ID}" method="post" >
<input type="hidden" name="send" value="on">
<fieldset style="width: 600px">
<legend>Delete Transporter: {USER}  /  {SERVER}</legend>
	<table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
		<tr>
			<td class="row2 last_td">
				<b>Are you sure you want to delete this transporter ?</b>
				<br />{USER}  /  {SERVER} 
				<br />
				<input type="checkbox" name="confirm">Confirm deletion</td>
			<td class="row1 last_td"  style="vertical-align: middle;">
				<input type="submit" class="small_btn" value="YES" style="flaot: left; margin-right:10px;">
				<input type="button" onclick="window.location = '{SITE_URL}/admin/system/transporter-list'"class="small_btn" value="Cancel">
			</td>
		</tr>
	</table>
</fieldset>
</form>


