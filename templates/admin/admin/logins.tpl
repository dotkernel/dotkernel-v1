<link rel="stylesheet" href="{SITE_URL}/externals/jquery/jquery.ui.tooltip.css">
<script src="{SITE_URL}/externals/jquery/jquery.ui.tooltip.js"></script>
<script>
	$(document).ready(function(){
		$(".icon[title]").tooltip();
	});
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
				<img src="{IMAGES_SHORT_URL}/flags/{COUNTRYIMAGE}.png"  border="0" id="ipc{ID}" style="margin-top:4px;" title="{COUNTRYNAME}" class="icon"/>
			</td>
			<td class="row{BG}" style="text-align: center;">
				<img src="{IMAGES_SHORT_URL}/browsers/{BROWSERIMAGE}.png" border="0" id="uab{ID}" style="margin-top:4px;" title="{USERAGENT} ({BROWSERIMAGE})" class="icon">
			</td>
			<td class="row{BG}" style="text-align: center;">
				<img src="{IMAGES_SHORT_URL}/os/{OSIMAGE}.png" border="0" id="os{ID}" style="margin-top:4px;" title="{OSMAJOR} {OSMINOR}" class="icon">
			</td>
			<td class="row{BG}" style="width: 150px;">{DATELOGIN}</td>
		</tr>
	<!-- END list -->
	</table>
	</fieldset>
</div>