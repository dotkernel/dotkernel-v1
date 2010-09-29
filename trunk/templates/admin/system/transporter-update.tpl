<form action="{SITE_URL}/admin/system/transporter-update/id/{ID}" method="post" >
<input type="hidden" name="send" value="on">
<fieldset style="width: 300px">
<legend>Update Email Transporter</legend>
	<table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
		<tr>
			<td><label>User</label><input type="text" name="user" value="{USER}" class="medium"></td>
		</tr>
		<tr>
			<td><label>Password</label><input type="text" name="pass" value="{PASS}" class="medium"></td>
		</tr>
		<tr>
			<td><label>Server</label><input type="text" name="server" value="{SERVER}" class="medium"></td>
		</tr>			
		<tr>
			<td><label>Port</label><input type="text" name="port" value="{PORT}" class="medium"></td>
		</tr>		
		<tr>
			<td><label>Capacity</label><input type="text" name="capacity" value="{CAPACITY}" class="medium"></td>
		</tr>		
    <tr>
      <td><label>SSL</label>
        TLS <input type="radio" name="ssl" value="tls" {SSL_TLS}> 
        SSL  <input type="radio" name="ssl" value="ssl" {SSL_SSL}>
      </td>
    </tr>
    <tr>
      <td><label>Active</label>
        Yes <input type="radio" name="isActive" value="1" {ACTIVE_1}> 
        No  <input type="radio" name="isActive" value="0" {ACTIVE_0}>
      </td>
    </tr>
		<tr>
			<td class="button_area">
				<input type="submit" onclick="" class="small_btn" value="update"></td>
		</tr>
	</table>
</fieldset>
</form>