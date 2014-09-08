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
	// define constants
	var SITE_URL = "{SITE_URL}",
		userToken = "{USERTOKEN}",
		ADD_URL = SITE_URL + "/admin/system/transporter-add/",
		FLAG_TOGGLE_URL = SITE_URL + "/admin/system/transporter-activate/";
</script>
<script>
	// active/inactive flags
	$(document).ready(function(){
		$(".activeButton").activeFlags({
			targetUrl:FLAG_TOGGLE_URL,
		});
	});
</script>
<script>
	// submit the form
	$(document).ready(function(){
		$("#transporterAddSubmit").click(function(){
			// clear any messages that might still be shown
			$("#messages").fadeOut(100);
			// post the form
			$.post(
				ADD_URL,
				$("#transporterAdd").serialize(),
				function(result){
					if (result.success){
						// the transporter was added
						$("#form-dialog").dialog("close");
						// add the new row
						$("#transporterTable").append(result.row);
						// clear the form
						$("#transporterAdd input[type=text]").val('');
					}else{
						// something went wrong
						var content;
						// generate the content for the error box
						content = "<ul><li>" + result.message.join("</li><li>") + "</li></ul>";
						// show the error
						$("#messages").removeClass().addClass("message_error").html(content).fadeIn();
					}
				},
				"json"
			)
		});
	});
</script>
<script>
	// show a dialog then the add new transporter button is clicked
	$(document).ready(function(){
		$("#dialogButton").click(function(){
			$("#form-dialog").dialog({
				modal: true
			});
		});
	})
</script>
<div id="adminList">
	{PAGINATION}
	<div class="box-shadow">
		<table class="big_table" frame="box" rules="all">
			<thead>
				<tr>
					<th style="text-align: center; width: 20px;">#</th>
					<th>User</th>
					<th>Server</th>
					<th>Port</th>
					<th>SSL</th>
					<th>Date</th>
					<th width="60px">Capacity</th>
					<th width="60px">Counter</th>
					<th width="50px">Status</th>
					<th width="130px">Action</th>
				</tr>
			</thead>
			<tbody id="transporterTable">
					{TRANSPORTER_ROW}
			</tbody>
		</table>
	</div>
</div>
<br>
<a id="dialogButton" class="button">Add Transporter</a>

<div id="form-dialog" title="Add Email Transporter">
	<form id="transporterAdd">
		<table class="medium_table round-left-right-bottom" style="width:100%; height:100%">
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
						<label>TLS<input type="radio" name="ssl" id="tsl" value="tls" checked></label>
						<label>SSL<input type="radio" name="ssl" id="ssl" value="ssl"></label>
					</td>
				</tr>
				<tr>
					<td>Active</td>
					<td>
					<label>Yes<input type="radio" id="active1" name="isActive" value="1" checked></label>
					<label>No<input type="radio" id="active0" name="isActive" value="0"></label>
					</td>
				</tr>
			<tr>
				<td></td>
				<td>
					<input type="button" class="button" value="Add" id="transporterAddSubmit">
				</td>
			</tr>
		</table>
	</form>
</div>
