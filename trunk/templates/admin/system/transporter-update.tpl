<form action="{SITE_URL}/admin/system/transporter-update/id/{ID}" method="post" >
<input type="hidden" name="send" value="on">
<fieldset style="width: 500px">
<legend>Update User Account</legend>
	<table cellpadding="0" cellspacing="1" class="big_table" width="100%">
		<tr>
			<td class="row2" width="130px"><b>User</b></td>
			<td class="row1"><input type="text" name="user" value="{USER}" class="bigger"></td>
		</tr>
		<tr>
			<td class="row2"><b>Password</b></td>
			<td class="row1"><input type="text" name="pass" value="{PASS}" class="bigger"></td>
		</tr>
		<tr>
			<td class="row2"><b>Server</b></td>
			<td class="row1"><input type="text" name="server" value="{SERVER}" class="bigger"></td>
		</tr>			
		<tr>
			<td class="row2"><b>Port</b></td>
			<td class="row1"><input type="text" name="port" value="{PORT}" class="bigger"></td>
		</tr>		
		<tr>
			<td class="row2"><b>Capacity</b></td>
			<td class="row1"><input type="text" name="capacity" value="{CAPACITY}" class="bigger"></td>
		</tr>		
    <tr>
      <td class="row2"><b>SSL</b></td>
      <td class="row1">
        TLS <input type="radio" name="ssl" value="tls" style="height: auto;" {SSL_TLS}> 
        SSL  <input type="radio" name="ssl" value="ssl" style="height: auto;" {SSL_SSL}>
      </td>
    </tr>
    <tr>
      <td class="row2"><b>Active</b></td>
      <td class="row1">
        Yes <input type="radio" name="isActive" value="1" style="height: auto;" {ACTIVE_1}> 
        No  <input type="radio" name="isActive" value="0" style="height: auto;" {ACTIVE_0}>
      </td>
    </tr>
		<tr>
			<td colspan="2" class="row1" style="text-align: center;">
				<input type="submit" onclick="" class="small_btn" value="update"></td>
		</tr>
	</table>
</fieldset>
</form>