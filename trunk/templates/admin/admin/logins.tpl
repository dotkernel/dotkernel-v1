<script type="text/javascript" src="{SITE_URL}/externals/dojo/dojo.xd.js">  </script>
 <script>
        dojo.require("dijit.Tooltip");
        dojo.require("dijit.form.Button");
    </script>
<div id="adminList">
	{PAGINATION}
	<fieldset style="width: 100%">
	<legend>List logins</legend>
	<table cellpadding="0" cellspacing="0" class="big_table" width="100%">
		<tr>
			<td class="table_subhead" style="text-align: center; width: 20px;"><span>#</span></td>
			<td class="table_subhead"><span>Username</span></td>
			<td class="table_subhead"><span>Referer</span></td>
			<td class="table_subhead" style="width: 150px;"><span>IP</span></td>
			<td class="table_subhead" style="width: 50px;"><span>Country</span></td>
			<td class="table_subhead" style="width: 50px;"><span>Browser</span></td>
			<td class="table_subhead" style="width: 50px;"><span>OS</span></td>
			<td class="table_subhead" style="width: 150px;"><span>Login Date</span></td>
		</tr>
	<!-- BEGIN list -->
		<tr>
			<td class="row{BG}" style="text-align: center;">{ID}</td>
			<td class="row{BG}"> <a href="{SITE_URL}/admin/admin/update/id/{ADMINID}">{USERNAME}</a> </td>
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
			</script></td>
			<td class="row{BG}" style="text-align: center;">
				<img src="{IMAGES_SHORT_URL}/browsers/{BROWSERIMAGE}.png" border="0" id="uab{ID}" style="margin-top:4px;">
				<script type="text/javascript">
				 dojo.addOnLoad(function() {
					  new dijit.Tooltip({
					     connectId: ["uab{ID}"],
					     label: "<span class='dijitTooltipBold'>User Agent:</span><br />{USERAGENT}"});});
				</script></td>
			<td class="row{BG}" style="text-align: center;">
				<img src="{IMAGES_SHORT_URL}/os/{OSIMAGE}.png" border="0" id="os{ID}" style="margin-top:4px;">
				<script type="text/javascript">
				 dojo.addOnLoad(function() {
					  new dijit.Tooltip({
					     connectId: ["os{ID}"],
					     label: "<span class='dijitTooltipBold'>Operating System: {OSMAJOR}</span><br />{OSMINOR}"});});
				</script></td>
			<td class="row{BG}" style="width: 150px;">{DATELOGIN}</td>
			</td>
		</tr>
	<!-- END list -->
	</table>
	</fieldset>
</div>