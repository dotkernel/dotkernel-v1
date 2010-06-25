<script type="text/javascript" src="{SITE_URL}/externals/dojo/dojo.xd.js"></script>
<script type="text/javascript" src="{TEMPLATES_URL}/js/admin/admin.js"></script>
<div id="adminList">
	{PAGINATION}
	<fieldset style="width: 100%">
	<legend>List Admins</legend>
	<table cellpadding="0" cellspacing="1" class="big_table">
		<tr>
			<td class="table_subhead"><span>#</span></td>
			<td class="table_subhead"><span>Username</span></td>
			<td class="table_subhead"><span>Email</span></td>
			<td class="table_subhead"><span>First name</span></td>
			<td class="table_subhead"><span>Last name</span></td>
			<td class="table_subhead"><span>Active</span></td>
			<td class="table_subhead"><span>Creation Date</span></td>
			<td class="table_subhead"><span>Action</span></td>
		</tr>
	<!-- BEGIN list -->
		<tr>
			<td class="row{BG}">{ID}</td>
			<td class="row{BG}"> <a href="{SITE_URL}/admin/admin/update/id/{ID}">{USERNAME}</a> </td>
			<td class="row{BG}">{EMAIL}</td>
			<td class="row{BG}">{FIRSTNAME}</td>
			<td class="row{BG}">{LASTNAME}</td>
			<td class="row{BG}" style="text-align: center;"> <a  onclick="javascript: adminList('{SITE_URL}{ACTIVE_URL}',{ID},{ISACTIVE},{PAGE});" style="cursor: pointer;" title="Activate / Inactivate">
				<img src="{IMAGES_URL}/{ACTIVE_IMG}.png" border='0'></a> </td>
			<td class="row{BG}">{DATE_CREATED}</td>
			<td class="row{BG}" > 
				<a href="{SITE_URL}/admin/admin/update/id/{ID}" title="Edit/Update"><img src="{IMAGES_URL}/edit.png" border='0' style="margin: 0px 10px;" ></a>
				<a href="{SITE_URL}/admin/admin/delete/id/{ID}" title="Delete"><img src="{IMAGES_URL}/delete.png" border='0' style="margin: 0px 10px;" ></a>
				<a href="{SITE_URL}/admin/admin/logins/id/{ID}" title="Admin Logins"><img src="{IMAGES_URL}/userLogins.png" border='0' style="margin: 0px 10px;" ></a>
			</td>
		</tr>
	<!-- END list -->
	</table>
	</fieldset>
</div>