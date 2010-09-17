<form action="{SITE_URL}/admin/system/transporter-delete/id/{ID}" method="post" >
<input type="hidden" name="send" value="on">
<fieldset style="width: 500px">
<legend>Delete Transporter</legend>
	<table cellpadding="0" cellspacing="1" class="big_table" width="100%">
		<tr>
			<td class="row2">
			  <b>Are you sure you want to delete this transporter ?</b>
			  <br/>
			  <strong>{USER}</strong> / <strong>{SERVER}</strong>
			</td>
		</tr>
		<tr>
			<td class="row1">
				Yes<input type="radio" value="1" name='delete' style="height: auto;" />
				No<input type="radio" value="0" name='delete' style="height: auto;" checked /></td>
		</tr>	
		<tr>
			<td class="row1" style="text-align: center;">
				<input type="submit" onclick="" class="small_btn" value="OK">
				</td>
		</tr>
	</table>
</fieldset>
</form>