<link rel="stylesheet" href="{SITE_URL}/externals/jquery/ui/jquery.ui.tooltip.min.css">
<script src="{SITE_URL}/externals/jquery/ui/jquery.ui.tooltip.min.js"></script>
<script>
	$(document).ready(function(){
		$(".icon[title]").tooltip();
	});
</script>
{PAGINATION}
<div id="adminList" class="box-shadow">
	<table class="big_table" frame="box" rules="all">
		<thead>
			<tr>
				<th style="text-align: center; width: 20px;">#</th>
				<th>Username</th>
				<th>Referer</th>
				<th style="width: 150px;">IP</th>
				<th style="width: 50px; text-align: center;">Country</th>
				<th style="width: 50px; text-align: center;">Browser</th>
				<th style="width: 50px; text-align: center;">OS</th>
				<th style="width: 150px;">Log In Date</th>
			</tr>
		</thead>
		<tbody>
		<!-- BEGIN list -->
			<tr>
				<td style="text-align: center;">{ID}</td>
				<td> <a href="{SITE_URL}/admin/admin/update/id/{ADMINID}">{USERNAME}</a> </td>
				<td>
					<input class="reffer_input" type="text" name="htmllink[]" value="{REFERER}" onclick="javascript:this.focus();this.select();" readonly>
	      		</td>
				<td>
					<a href="{WHOISURL}/{IP}" target="_blank">{IP}</a></td>
				<td style="text-align: center;">
					<img src="{IMAGES_SHORT_URL}/flags/{COUNTRYIMAGE}.png"  border="0" id="ipc{ID}" style="margin-top:4px;" title="{COUNTRYNAME}" class="icon"/>
				</td>
				<td style="text-align: center;">
					<img src="{IMAGES_SHORT_URL}/browsers/{BROWSERIMAGE}.png" border="0" id="uab{ID}" style="margin-top:4px;" title="{USERAGENT} ({BROWSERIMAGE})" class="icon">
				</td>
				<td style="text-align: center;">
					<img src="{IMAGES_SHORT_URL}/os/{OSIMAGE}.png" border="0" id="os{ID}" style="margin-top:4px;" title="{OSMAJOR} {OSMINOR}" class="icon">
				</td>
				<td style="width: 150px;">{DATELOGIN}</td>
			</tr>
		<!-- END list -->
		</tbody>
	</table>
</div>