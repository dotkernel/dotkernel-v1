<script type="text/javascript" src="{SITE_URL}/externals/dojo/dojo.xd.js"></script>
<script type="text/javascript" src="{TEMPLATES_URL}/js/admin/admin.js"></script>
<script type="text/javascript" src="{TEMPLATES_URL}/js/admin/system.js"></script>
<style type="text/css">@import "{TEMPLATES_URL}/css/admin/dojo.css";</style>
<div id="adminList">
	{AJAX_MESSAGE_BLOCK}
	{PAGINATION}
	<table style="width:100%">
	  <tr>
	    <td style="padding-right:20px;">
      	<fieldset style="width: 100%">
      	<legend>List Email Transporters</legend>
      	<table cellpadding="0" cellspacing="0" class="big_table" width="100%">
      		<tr>
      			<td class="table_subhead" style="text-align: center;"><span>#</span></td>
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
            <table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
              <tr>
                <td><label>User</label><input type="text" name="user" value="" class="medium"></td>
              </tr>
              <tr>
                <td><label>Pass</label><input type="text" name="pass" value="" class="medium"></td>
              </tr>
              <tr>
                <td><label>Server</label><input type="text" name="server" value="" class="medium"></td>
              </tr>     
              <tr>
                <td><label>Port</label><input type="text" name="port" value="" class="medium"></td>
              </tr>   
              <tr>
                <td><label>SSL</label>
                  <span>TLS</span> <input type="radio" name="ssl" value="tls" checked="checked"> 
                  <span>SSL</span>  <input type="radio" name="ssl" value="ssl">
                </td>
              </tr>
              <tr>
                <td><label>Capacity</label><input type="text" name="capacity" value="" class="medium"></td>
              </tr>   
              <tr>
                <td><label>Active</label>
                  <span>Yes</span> <input type="radio" name="isActive" value="1" checked="checked"> 
                  <span>No</span>  <input type="radio" name="isActive" value="0">
                </td>
              </tr>
              <tr>
                <td class="button_area">
                  <input type="button" onclick="adminAddTransporter('{SITE_URL}/admin/system/transporter-add',{PAGE});" class="small_btn" value="Add"></td>
              </tr>
            </table>
        </fieldset>
        </form>
      </td>
    </tr>
  </table>
</div>