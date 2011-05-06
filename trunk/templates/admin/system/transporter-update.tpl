<form action="{SITE_URL}/admin/system/transporter-update/id/{ID}" method="post" >
<input type="hidden" name="send" value="on">
<fieldset style="width: 500px">
<legend>Update Email Account</legend>
	<table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
		<tr>
			<td width="130px"><b>User</b></td>
			<td><input type="text" name="user" value="{USER}" ></td>
		</tr>
		<tr>
			<td><b>Password</b></td>
			<td><input type="text" name="pass" value="{PASS}" ></td>
		</tr>
		<tr>
			<td><b>Server</b></td>
			<td><input type="text" name="server" value="{SERVER}" ></td>
		</tr>			
		<tr>
			<td><b>Port</b></td>
			<td><input type="text" name="port" value="{PORT}" ></td>
		</tr>		
		<tr>
			<td><b>Capacity</b></td>
			<td><input type="text" name="capacity" value="{CAPACITY}" ></td>
		</tr>		
	    <tr>
	      <td><b>SSL</b></td>
	      <td>
	         <label for="tsl">TLS</label> <input type="radio" name="ssl" id="tsl" value="tls"  {SSL_TLS}> 
	         <label for="ssl">SSL</label>  <input type="radio" name="ssl" id="ssl" value="ssl"  {SSL_SSL}>
	      </td>
	    </tr>
	    <tr>
	      <td><b>Active</b></td>
	      <td>
	       <label for="active1">Yes</label> <input type="radio" id="active1" name="isActive" value="1"  {ACTIVE_1}> 
	       <label for="active0">No</label>  <input type="radio" id="active0" name="isActive" value="0"  {ACTIVE_0}>
	      </td>
	    </tr>
		<tr>
			<td> </td>
			<td class="row1 last_td" >
				<input type="submit" onclick="" class="small_btn" value="update"></td>
		</tr>
	</table>
</fieldset>
</form>