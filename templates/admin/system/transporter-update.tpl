<form action="{SITE_URL}/admin/system/transporter-update/id/{ID}" method="post" >
<fieldset style="width: 500px">
<legend>Update Email Account</legend>
	<table class="medium_table">
		<tr>
			<td width="130px">User</td>
			<td><input type="text" name="user" value="{USER}" ></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="text" name="pass" value="{PASS}" ></td>
		</tr>
		<tr>
			<td>Server</td>
			<td><input type="text" name="server" value="{SERVER}" ></td>
		</tr>
		<tr>
			<td>Port</td>
			<td><input type="text" name="port" value="{PORT}" ></td>
		</tr>
		<tr>
			<td>Capacity</td>
			<td><input type="text" name="capacity" value="{CAPACITY}" ></td>
		</tr>
		<tr>
			<td>SSL</td>
			<td>
				<label for="tsl">TLS</label> <input type="radio" name="ssl" id="tsl" value="tls" {SSL_TLS}>
				<label for="ssl">SSL</label>  <input type="radio" name="ssl" id="ssl" value="ssl" {SSL_SSL}>
			</td>
		</tr>
		<tr>
			<td>Active</td>
			<td>
				<label for="active1">Yes</label> <input type="radio" id="active1" name="isActive" value="1" {ACTIVE_1}>
				<label for="active0">No</label>  <input type="radio" id="active0" name="isActive" value="0" {ACTIVE_0}>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" class="button" value="update">
			</td>
		</tr>
	</table>
</fieldset>
</form>
