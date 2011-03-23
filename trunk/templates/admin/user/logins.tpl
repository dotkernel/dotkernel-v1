<script type="text/javascript" src="{TEMPLATES_URL}/js/admin/admin.js"></script>
 <script>
        dojo.require("dijit.Tooltip");
        dojo.require("dijit.form.Button");    
        dojo.require("dijit.form.DateTextBox");
        dojo.require("dojo.date.locale"); 		             
</script>
	
<div id="adminList">	
	<table class="g_box" cellpadding="0" cellspacing="1">
		<tr>
			<td >
				<form action="{FORM_ACTION}" method="post" name="logins">
				  Filter by browser:&nbsp;
					<select name="browser" id="browser" onchange="javascript: adminLogins('{SITE_URL}{FILTER_URL}',1, this.value, dijit.byId('filterDate').value);">
						<option value=""> - no filter - </option>
						<!-- BEGIN browser -->
						<option value="{BROWSERNAME}" {BROWSERSEL}> {BROWSERNAME}
						<!-- END browser -->
					</select>
				  Filter by date:&nbsp;         
					<input type="text" name="filterDate" id="filterDate" dojoType="dijit.form.DateTextBox" value="{FILTERDATE}"
							onchange="javascript: adminLogins('{SITE_URL}{FILTER_URL}',1, dojo.byId('browser').value, this.value);" />
					<label for="filterDate">
					    <img src="{IMAGES_URL}/calendar.png" border='0' style="margin-top: 1px;"/>
					</label>
				</form>							
			</td>
		</tr>
	</table>
	{PAGINATION}
	<fieldset style="width: 100%">
	<legend>List logins</legend>
	<table cellpadding="0" cellspacing="0" class="big_table" width="100%">
		<tr>
			<td class="table_subhead" style="text-align: center; width: 20px;"><span>#</span></td>
			<td class="table_subhead"><span><a href="{SITE_URL}{LINK_SORT_USERNAME}" class="{CLASS_SORT_USERNAME}">Username</a></span></td>
			<td class="table_subhead"><span>Referer</span></td>
			<td class="table_subhead" style="width: 150px;"><span>IP</span></td>
			<td class="table_subhead" style="width: 50px;"><span>Country</span></td>
			<td class="table_subhead" style="width: 50px;"><span>Browser</span></td>
			<td class="table_subhead" style="width: 50px;"><span>OS</span></td>
			<td class="table_subhead" style="width: 150px;"><span><a href="{SITE_URL}{LINK_SORT_DATELOGIN}" class="{CLASS_SORT_DATELOGIN}">Login Date</a></span></td>
		</tr>
	<!-- BEGIN list -->
		<tr>
			<td class="row{BG}" align="center">{ID}</td>
			<td class="row{BG}"> <a href="{SITE_URL}/admin/user/update/id/{USERID}">{USERNAME}</a> </td>
			<td class="row{BG}">
				<input class="reffer_input" type="text" name="htmllink[]" value="{REFERER}" onclick="javascript:this.focus();this.select();" readonly>
      		</td>
			<td class="row{BG}">				
				<a href="{WHOISURL}/{IP}" target="_blank">{IP}</a></td>
			<td class="row{BG}" style="text-align: center;">
				<img src="{IMAGES_SHORT_URL}/flags/{COUNTRYIMAGE}.png"  border="0" id="ipc{ID}" align="center" style="margin-top:4px;"/>
				<script type="text/javascript">
					 dojo.addOnLoad(function() {
						  new dijit.Tooltip({
						     connectId: ["ipc{ID}"],
						     label: "<span class='dijitTooltipBold'>Country:</span><br />{COUNTRYNAME}"});});
				</script>
			</td>
			<td class="row{BG}" style="text-align: center;">
				<img src="{IMAGES_SHORT_URL}/browsers/{BROWSERIMAGE}.png" border="0" id="uab{ID}" style="margin-top:4px;">
				<script type="text/javascript">
				 dojo.addOnLoad(function() {
					  new dijit.Tooltip({
					     connectId: ["uab{ID}"],
					     label: "<span class='dijitTooltipBold'>User Agent:</span><br />{USERAGENT}"});});
				</script>
			</td>
			<td class="row{BG}" style="text-align: center;">
				<img src="{IMAGES_SHORT_URL}/os/{OSIMAGE}.png" border="0" id="os{ID}" style="margin-top:3px;">
				<script type="text/javascript">
				 dojo.addOnLoad(function() {
					  new dijit.Tooltip({
					     connectId: ["os{ID}"],
					     label: "<span class='dijitTooltipBold'>Operating System: {OSMAJOR}</span><br />{OSMINOR}"}); });
				</script>
			</td>
			<td class="row{BG}">{DATELOGIN}</td>
		</tr>
	<!-- END list -->
	</table>
	</fieldset>
</div>