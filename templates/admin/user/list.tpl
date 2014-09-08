<script>
	var userToken = "{USERTOKEN}",
		SITE_URL = "{SITE_URL}",
		FLAG_TOGGLE_URL = SITE_URL + "/admin/user/activate/";

	$(document).ready(function(){
		$(".activeButton").activeFlags({
			targetUrl:FLAG_TOGGLE_URL,
		});
	})
</script>
{PAGINATION}
<div id="adminList" class="box-shadow">
	<table class="big_table" frame="box" rules="all">
		<thead>
			<tr>
				<th style="text-align: center; width: 20px;"><span>#</span></th>
				<th><span>Username</span></th>
				<th><span>Email</span></th>
				<th><span>First name</span></th>
				<th><span>Last name</span></th>
				<th><span>Creation Date</span></th>
				<th style="text-align: center;" width="50px"><span>Status</span></th>
				<th width="300px"><span>Action</span></th>
			</tr>
		</thead>
		<tbody>
		<!-- BEGIN list -->
			<tr>
				<td style="text-align: center;">{ID}</td>
				<td><a href="{SITE_URL}/admin/user/update/id/{ID}">{USERNAME}</a> </td>
				<td>{EMAIL}</td>
				<td>{FIRSTNAME}</td>
				<td>{LASTNAME}</td>
				<td>{DATE_CREATED}</td>
				<td style="vertical-align: middle; text-align: left;">
					<a style="cursor: pointer;" title="Activate / Deactivate" class="{ACTIVE_IMG}_state activeButton" data-id="{ID}" data-active="{ISACTIVE}">&nbsp;</a>
				</td>
				<td>
					<table class="action_table">
						<tr>
							<td width="20%"><a href="{SITE_URL}/admin/user/update/id/{ID}/" title="Edit/Update" class="edit_state">Edit</a></td>
							<td width="25%"><a href="{SITE_URL}/admin/user/delete/id/{ID}/" title="Delete" class="delete_state">Delete</a></td>
							<td width="25%"><a href="{SITE_URL}/admin/user/logins/id/{ID}/" title="User Log In" class="logins_state">Logins</a></td>
							<td width="30%"><a href="{SITE_URL}/admin/user/send-password/id/{ID}/" title="Send User Password" class="pass_state">Send Pass</a></td>
							</tr>
					</table>
				</td>
			</tr>
		<!-- END list -->
		</tbody>
	</table>
</div>