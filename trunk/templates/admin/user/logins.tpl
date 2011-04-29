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
			buttonImage: "{IMAGES_URL}/calendar.png",
			buttonImageOnly: true,
			onSelect: updateFilters
		});
		
		$("#browser").change(updateFilters);
	});
</script>
<div id="adminList">	
	<table class="g_box" cellpadding="0" cellspacing="1">
		<tr>
			<td >
				<form action="{FORM_ACTION}" method="post" name="logins">
				  Filter by browser:&nbsp;
					<select name="browser" id="browser">
						<option value=""> - no filter - </option>
						<!-- BEGIN browser -->
						<option value="{BROWSERNAME}" {BROWSERSEL}> {BROWSERNAME} </option>
						<!-- END browser -->
					</select>
					Filter by date:&nbsp;
					<input type="text" id="filterDate" value="{FILTERDATE}">
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
				/*
						     connectId: ["ipc{ID}"],
						     label: "<span class='dijitTooltipBold'>Country:</span><br />{COUNTRYNAME}"});});
				*/
				</script>
			</td>
			<td class="row{BG}" style="text-align: center;">
				<img src="{IMAGES_SHORT_URL}/browsers/{BROWSERIMAGE}.png" border="0" id="uab{ID}" style="margin-top:4px;">
				<script type="text/javascript">
				/*
					     connectId: ["uab{ID}"],
					     label: "<span class='dijitTooltipBold'>User Agent:</span><br />{USERAGENT}"});});
				*/
				</script>
			</td>
			<td class="row{BG}" style="text-align: center;">
				<img src="{IMAGES_SHORT_URL}/os/{OSIMAGE}.png" border="0" id="os{ID}" style="margin-top:3px;">
				<script type="text/javascript">
				/*
					     connectId: ["os{ID}"],
					     label: "<span class='dijitTooltipBold'>Operating System: {OSMAJOR}</span><br />{OSMINOR}"}); });
				*/
				</script>
			</td>
			<td class="row{BG}">{DATELOGIN}</td>
		</tr>
	<!-- END list -->
	</table>
	</fieldset>
</div>