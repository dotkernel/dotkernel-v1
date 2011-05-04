<script type="text/javascript" src="{TEMPLATES_URL}/js/admin/system.js"></script>
<div id="adminList">
	{AJAX_MESSAGE_BLOCK}
	{PAGINATION}
	<table style="width:100%">
		<tr>
			<td style="padding-right:20px;">
				<fieldset style="width: 100%">
				<legend>List Email Transporters</legend>
				<table class="big_table">
					<thead>
						<tr>
							<th style="text-align: center;">#</th>
							<th>User</th>
							<th>Server</th>
							<th>Port</th>
							<th>SSL</th>
							<th>Date</th>
							<th>Capacity</th>
							<th>Counter</th>
							<th>Active</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<!-- BEGIN list -->
						<tr>
							<td style="text-align: center;">{ID}</td>
							<td> <a href="{SITE_URL}/admin/system/transporter-update/id/{ID}">{USER}</a> </td>
							<td>{SERVER}</td>
							<td>{PORT}</td>
							<td>{SSL}</td>
							<td>{DATE_CREATED}</td>
							<td style="text-align:right">{CAPACITY}</td>
							<td style="text-align:right">{COUNTER}</td>
							<td style="vertical-align: middle;"> <a	onclick="javascript: adminList('{SITE_URL}{ACTIVE_URL}',{ID},{ISACTIVE},{PAGE});" style="cursor: pointer;" title="Activate / Inactivate"	class="{ACTIVE_IMG}_state">&nbsp;</a> </td>
							<td > 
								<table width="100%" class="action_table">
									<tr>
										<td width="25%"><a href="{SITE_URL}/admin/system/transporter-update/id/{ID}" title="Edit/Update" class="edit_state">&nbsp;</a></td>
										<td width="25%"><a href="{SITE_URL}/admin/system/transporter-delete/id/{ID}" title="Delete" class="delete_state">&nbsp;</a></td>
									</tr>
								</table>
							</td>
						</tr>
						<!-- END list -->
					</tbody>
				</table>
				</fieldset>
			</td>
			<td style="width:250px">
				<form id="transporterAdd">
				<fieldset style="width: 100%">
					<legend>Add Email Transporter</legend>
					<table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
						<tr>
							<td class="row2"><b>User</b></td>
							<td class="row1"><input type="text" name="user" value="{USER}" ></td>
						</tr>
						<tr>
							<td class="row2"><b>Password</b></td>
							<td class="row1"><input type="text" name="pass" value="{PASS}" ></td>
						</tr>
						<tr>
							<td class="row2"><b>Server</b></td>
							<td class="row1"><input type="text" name="server" value="{SERVER}" ></td>
						</tr>
						<tr>
							<td class="row2"><b>Port</b></td>
							<td class="row1"><input type="text" name="port" value="{PORT}" ></td>
						</tr>
						<tr>
							<td class="row2"><b>Capacity</b></td>
							<td class="row1"><input type="text" name="capacity" value="{CAPACITY}" ></td>
						</tr>
							<tr>
								<td class="row2"><b>SSL</b></td>
								<td class="row1">
									<label>TLS<input type="radio" name="ssl" id="tsl" value="tls" {SSL_TLS}></label>
									<label>SSL<input type="radio" name="ssl" id="ssl" value="ssl" {SSL_SSL}></label>
								</td>
							</tr>
							<tr>
								<td class="row2"><b>Active</b></td>
								<td class="row1">
								<label>Yes<input type="radio" id="active1" name="isActive" value="1" {ACTIVE_YES}></label>
								<label>No<input type="radio" id="active0" name="isActive" value="0" {ACTIVE_NO}></label>
								</td>
							</tr>
						<tr>
							<td class="row2"></td>
							<td class="row1 last_td">
								<input type="button" onclick="adminAddTransporter('{SITE_URL}/admin/system/transporter-add',{PAGE});" class="small_btn" value="Add">
							</td>
						</tr>
					</table>
				</fieldset>
				</form>
			</td>
		</tr>
	</table>
</div>