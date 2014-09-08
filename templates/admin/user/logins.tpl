<link rel="stylesheet" href="{SITE_URL}/externals/jquery/ui/jquery.ui.tooltip.min.css">
<script src="{SITE_URL}/externals/jquery/ui/jquery.ui.tooltip.min.js"></script>
<script>
	var SITE_URL = "{SITE_URL}",
		FILTER_URL = "{FILTER_URL}";

	function updateFilters(){
		var date = $.datepicker.formatDate("yy-mm-dd", $($("#filterDate")).datepicker("getDate")),
			browser = $("#browser").val();
			url = SITE_URL + FILTER_URL;
		
		if (date !== ""){
			url += '/loginDate/' + date;
		}
		
		if (browser !== ""){
			url += '/browser/' + browser;
		}
		window.location = url;
	}

	$(document).ready(function(){
		$("#filterDate").datepicker({
			showOn: "both",
			dateFormat: 'yy-mm-dd',
			buttonImage: "{TEMPLATES_URL}/css/admin/{SKIN}/images/calendar.png",
			buttonImageOnly: true,
			onSelect: updateFilters
		});
		
		$("#browser").change(updateFilters);

		//// tooltips

		$(".icon[title]").tooltip();

	});
</script>
<div id="adminList">
	<table class="g_box">
		<tr>
			<td class="g_box_head" width="55px">
				Filters
			</td>
			<td>
				<form action="{FORM_ACTION}" method="post" name="logins">
				Browser:&nbsp;
					<select name="browser" id="browser">
						<option value=""> - no filter - </option>
						<!-- BEGIN browser -->
						<option value="{BROWSERNAME}" {BROWSERSEL}> {BROWSERNAME} </option>
						<!-- END browser -->
					</select>
					Date:&nbsp;
					<input type="text" id="filterDate" value="{FILTERDATE}">
				</form>
			</td>
		</tr>
	</table>
	
	{PAGINATION}
	<div class="box-shadow">
		<table class="big_table" frame="box" rules="all">
			<thead>
				<tr>
					<th style="text-align: center; width: 20px;">#</th>
					<th><a href="{SITE_URL}{LINK_SORT_USERNAME}" class="{CLASS_SORT_USERNAME}">Username</a></th>
					<th>Referer</th>
					<th style="width: 150px;">IP</th>
					<th style="width: 50px; text-align: center;">Country</th>
					<th style="width: 50px; text-align: center;">Browser</th>
					<th style="width: 50px; text-align: center;">OS</th>
					<th style="width: 150px;"><a href="{SITE_URL}{LINK_SORT_DATELOGIN}" class="{CLASS_SORT_DATELOGIN}">Login Date</a></th>
				</tr>
			</thead>
			<tbody>
				<!-- BEGIN list -->
					<tr>
						<td align="center">{ID}</td>
						<td> <a href="{SITE_URL}/admin/user/update/id/{USERID}">{USERNAME}</a> </td>
						<td>
							<input class="reffer_input" type="text" name="htmllink[]" value="{REFERER}" onclick="javascript:this.focus();this.select();" readonly>
						</td>
						<td>
							<a href="{WHOISURL}/{IP}" target="_blank">{IP}</a></td>
						<td style="text-align: center;">
							<img src="{IMAGES_SHORT_URL}/flags/{COUNTRYIMAGE}.png"  border="0" id="ipc{ID}" style="margin-top:4px;" title="{COUNTRYNAME}" class="icon"/>
						</td>
						<td style="text-align: center;">
							<img src="{IMAGES_SHORT_URL}/browsers/{BROWSERIMAGE}.png" border="0" id="uab{ID}" style="cursor:pointer;margin-top:3px;" title="{USERAGENT}" class="icon">
						</td>
						<td style="text-align: center;">
							<img src="{IMAGES_SHORT_URL}/os/{OSIMAGE}.png" border="0" id="os{ID}" style="margin-top:3px;" class="icon" title="{OSMAJOR} {OSMINOR}">
						</td>
						<td>{DATELOGIN}</td>
					</tr>
				<!-- END list -->
			</tbody>
		</table>
	</div>
</div>
