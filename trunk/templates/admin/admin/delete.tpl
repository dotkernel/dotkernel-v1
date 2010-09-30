<form action="{SITE_URL}/admin/admin/delete/id/{ID}" method="post" >
<input type="hidden" name="send" value="on">
<fieldset style="width: 350px">
<legend>Delete Admin Acccount: {USERNAME}</legend>
	<table cellpadding="0" cellspacing="0" class="big_table" width="100%">
		<tr>
			<td class="row2 bordered"><b>Are you sure you want to delete this account ?</b></td>
		</tr>
		<tr>
			<td class="row1">
				Yes<input type="radio" value="1" name='delete' style="height: auto;" />
				No<input type="radio" value="0" name='delete' style="height: auto;" checked /></td>
		</tr>	
		<tr>
			<td class="big_button_area">
				<input type="submit" onclick="" class="small_btn" value="Yes">
				</td>
		</tr>
	</table>
</fieldset>
</form>