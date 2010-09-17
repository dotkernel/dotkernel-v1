<script type="text/javascript" src="{SITE_URL}/externals/dojo/dojo.xd.js"></script>
<script type="text/javascript" src="{TEMPLATES_URL}/js/admin/system.js"></script>
<div id="adminList">
	{AJAX_MESSAGE_BLOCK}
	{PAGINATION}
	<table style="width:100%">
	  <tr>
	    <td style="padding-right:20px;">
      	<fieldset style="width: 100%">
      	<legend>List Users</legend>
      	<table cellpadding="0" cellspacing="1" class="big_table" width="100%">
      		<tr>
      			<td class="table_subhead"><span>#</span></td>
      			<td class="table_subhead"><span>User</span></td>
      			<td class="table_subhead"><span>Server</span></td>
      			<td class="table_subhead"><span>Port</span></td>
      			<td class="table_subhead"><span>SSL</span></td>
      			<td class="table_subhead"><span>Date</span></td>
      			<td class="table_subhead"><span>Capacity</span></td>
      			<td class="table_subhead"><span>Counter</span></td>
            <td class="table_subhead"><span>Active</span></td>
            <td class="table_subhead"><span>Action</span></td>
      		</tr>
      	<!-- BEGIN list -->
      		<tr>
      			<td class="row{BG}" style="text-align: center;">{ID}</td>
      			<td class="row{BG}"> <a href="{SITE_URL}/admin/system/transporter-update/id/{ID}">{USER}</a> </td>
      			<td class="row{BG}">{SERVER}</td>
      			<td class="row{BG}">{PORT}</td>
            <td class="row{BG}">{SSL}</td>
            <td class="row{BG}">{DATE_CREATED}</td>
            <td class="row{BG}" style="text-align:right">{CAPACITY}</td>
            <td class="row{BG}" style="text-align:right">{COUNTER}</td>
      			<td class="row{BG}" style="vertical-align: middle;"> <a  onclick="javascript: adminList('{SITE_URL}{ACTIVE_URL}',{ID},{ISACTIVE},{PAGE});" style="cursor: pointer;" title="Activate / Inactivate"  class="{ACTIVE_IMG}_state">&nbsp;</a> </td>
      			<td class="row{BG}" > 
        			<table width="100%" class="action_table">
        				<tr>
        					<td width="25%"><a href="{SITE_URL}/admin/system/transporter-update/id/{ID}" title="Edit/Update" class="edit_state">&nbsp;</a></td>
        					<td width="25%"><a href="{SITE_URL}/admin/system/transporter-delete/id/{ID}" title="Delete" class="delete_state">&nbsp;</a></td>
          			</tr>
        			</table>
      			</td>
      		</tr>
      	<!-- END list -->
      	</table>
      	</fieldset>
      </td>
      <td style="width:250px">
        <form id="transporterAdd">
        <fieldset style="width: 100%">
          <legend>Add User</legend>
            <table cellpadding="0" cellspacing="1" class="big_table" width="100%">
              <tr>
                <td class="row2"><b>User</b></td>
                <td class="row1"><input type="text" name="user" value="" style="width:100%"></td>
              </tr>
              <tr>
                <td class="row2"><b>Pass</b></td>
                <td class="row1"><input type="text" name="pass" value="" style="width:100%"></td>
              </tr>
              <tr>
                <td class="row2"><b>Server</b></td>
                <td class="row1"><input type="text" name="server" value="" style="width:100%"></td>
              </tr>     
              <tr>
                <td class="row2"><b>Port</b></td>
                <td class="row1"><input type="text" name="port" value="" style="width:100%"></td>
              </tr>   
              <tr>
                <td class="row2"><b>SSL</b></td>
                <td class="row1">
                  TLS <input type="radio" name="ssl" value="tls" style="height: auto;" checked="checked"> 
                  SSL  <input type="radio" name="ssl" value="ssl" style="height: auto;">
                </td>
              </tr>
              <tr>
                <td class="row2"><b>Capacity</b></td>
                <td class="row1"><input type="text" name="capacity" value="" style="width:100%"></td>
              </tr>   
              <tr>
                <td class="row2"><b>Active</b></td>
                <td class="row1">
                  Yes <input type="radio" name="isActive" value="1" style="height: auto;" checked="checked"> 
                  No  <input type="radio" name="isActive" value="0" style="height: auto;">
                </td>
              </tr>
              <tr>
                <td colspan="2" class="row1"  style="text-align: center;">
                  <input type="button" onclick="adminAddTransporter('{SITE_URL}/admin/system/transporter-add',{PAGE});" class="small_btn" value="Add"></td>
              </tr>
            </table>
        </fieldset>
        </form>
      </td>
    </tr>
  </table>
</div>