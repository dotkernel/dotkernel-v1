<style>
	#messages{
		display:none;
		margin:0;
		text-transform:none;
	}
	#form-dialog{
		display:none;
		padding:0;
	}
	.button{
		cursor:pointer;
	}
</style>
<script>
	var SITE_URL = "{SITE_URL}",
		userToken = "{USERTOKEN}",
		ADD_URL = SITE_URL + "/admin/system/transporter-add/"
		FLAG_TOGGLE_URL = SITE_URL + "/admin/system/transporter-activate/";
	
	$(document).ready(function(){
		$(".activeButton").activeFlags({
			targetUrl:FLAG_TOGGLE_URL,
		});

		$("#transporterAddSubmit").click(function(){
			$("#messages").fadeOut(100);
			$.post(
				ADD_URL,
				$("#transporterAdd").serialize(),
				function(result){
					var content;
					content = "<ul><li>" + result.message.join("</li><li>") + "</li></ul>";
					console.log(result.data);
					if (result.success){
						//$("#messages").removeClass().addClass("message_info").html(content).fadeIn();
						$("#form-dialog").dialog("close");
						$("#transporterTable").append(result.row);
						$("#transporterAdd input[type=text]").val('');
					}else{
						$("#messages").removeClass().addClass("message_error").html(content).fadeIn();
					}
				},
				"json"
			)
		});

		$("#dialogButton").click(function(){
			$("#form-dialog").dialog({
				modal: true
			});
		});
	})
</script>
<div id="adminList">
	{PAGINATION}
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
		<tbody id="transporterTable">
				{TRANSPORTER_ROW}
		</tbody>
	</table>
	</fieldset>
</div>

<a id="dialogButton" class="button">Add Transporter</a>

<div id="form-dialog" title="Add Email Transporter">
	<form id="transporterAdd">
		<table class="medium_table" style="width:100%;height:100%">
			<tr>
				<td colspan="2" style="padding:0">
					<div id="messages"></div>
				</td>
			</tr>
			<tr>
				<td style="width:100px">User</td>
				<td><input type="text" name="user" id="user"></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="text" name="pass" id="pass"></td>
			</tr>
			<tr>
				<td>Server</td>
				<td><input type="text" name="server" id="server"></td>
			</tr>
			<tr>
				<td>Port</td>
				<td><input type="text" name="port" id="port" value="25"></td>
			</tr>
			<tr>
				<td>Capacity</td>
				<td><input type="text" name="capacity" id="capacity" value="2000"></td>
			</tr>
				<tr>
					<td>SSL</td>
					<td>
						<label>TLS<input type="radio" name="ssl" id="tsl" value="tls" {SSL_TLS}></label>
						<label>SSL<input type="radio" name="ssl" id="ssl" value="ssl" {SSL_SSL}></label>
					</td>
				</tr>
				<tr>
					<td>Active</td>
					<td>
					<label>Yes<input type="radio" id="active1" name="isActive" value="1" {ACTIVE_YES}></label>
					<label>No<input type="radio" id="active0" name="isActive" value="0" {ACTIVE_NO}></label>
					</td>
				</tr>
			<tr>
				<td></td>
				<td class="row1 last_td">
					<input type="button" class="small_btn" value="Add" id="transporterAddSubmit">
				</td>
			</tr>
		</table>
	</form>
</div>
