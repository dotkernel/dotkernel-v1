{PAGINATION}
<fieldset>
<legend>Table head</legend>
<table cellpadding="0" cellspacing="1" class="big_table" width="100%">
	<tr>
		<td class="table_subhead"><span>ID</span></td>
		<td class="table_subhead"><span>Username</span></td>
		<td class="table_subhead"><span>Email</span></td>
		<td class="table_subhead"><span>First name</span></td>
		<td class="table_subhead"><span>Last name</span></td>
		<td class="table_subhead"><span>Active</span></td>
		<td class="table_subhead"><span>Creation Date</span></td>
	</tr>
<!-- BEGIN list -->
	<tr>
		<td class="row{BG}">{ID}</td>
		<td class="row{BG}">[ <a href="{SITE_URL}/admin/user/update/id/{ID}">{USERNAME}</a></a> ]</td>
		<td class="row{BG}">{EMAIL}</td>
		<td class="row{BG}">{FIRSTNAME}</td>
		<td class="row{BG}">{LASTNAME}</td>
		<td class="row{BG}"><img src="{IMAGES_URL}/{ACTIVE_IMG}.png" border='0'></td>
		<td class="row{BG}">{DATE_CREATED}</td>
	</tr>
<!-- END list -->
</table>