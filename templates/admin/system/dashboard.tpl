<script>
	$(document).ready(function(){
		// update jQuery and jQuery UI versions
		$("#jqueryVersion").text($().jquery);
		$("#jqueryUiVersion").text($.ui.version);
	});
</script>

<table class="dashboard_table">
	<tr>
		<td class="sys_td">
			<div class="system_overview">
				<div class="box-shadow">
					<div class="box_header">
						System Overview
					</div>
					<table class="medium_table">
						<tr>
							<td width="150px">HOSTNAME</td>
							<td>{HOSTNAME}</td>
						</tr>
						<tr>
							<td>PHP</td>
							<td>{PHP} ({PHPAPI}) &nbsp;&nbsp;[ <a href="{SITE_URL}/admin/system/phpinfo">PHP Info</a> ]</td>
						</tr>
						<tr>
							<td>{APCNAME}</td>
							<td>{APCVERSION} ({APCSTATUS}) &nbsp;&nbsp;[ <a href="{SITE_URL}/admin/system/apc-info/">APC Info</a> ]</td>
						</tr>
						<tr>
							<td>MYSQL</td>
							<td>MYSQL {MYSQL}</td>
						</tr>
						<tr>
							<td>Zend Framework</td>
							<td>{ZFVERSION}</td>
						</tr>
					</table>
					<table class="medium_table no_border-top">
						<tr>
							<td width="150px">GEOIP COUNTRY LOCAL</td>
							<td>{GEOIP_COUNTRY_LOCAL}</td>
						</tr>
						<!-- BEGIN is_geoip -->
						<tr>
							<td>GEOIP CITY</td>
							<td>{GEOIP_CITY_VERSION}</td>
						</tr>
						<tr>
							<td>GEOIP COUNTRY</td>
							<td>{GEOIP_COUNTRY_VERSION}</td>
						</tr>
						<!-- END is_geoip -->
					</table>
					<!-- BEGIN wurfl_api_info -->
					<table class="medium_table no_border-top">
						<tr>
							<td>WURFL API VERSION</td>
							<td>{WURFLAPIVERSION}</td>
						</tr>
						<tr>
							<td width="150px">WURFL Cache DATE</td>
							<td>{WURFLCACHEBUILT}     [ <a href="{SITE_URL}/admin/system/build-wurfl-cache">rebuild</a> ]
									[ <a href="{SITE_URL}/admin/system/empty-wurfl-cache">empty</a> ]</td>
						</tr>
						<tr>
							<td>WURFL XML FILE</td>
							<td>{WURFLDATE}</td>
						</tr>
					</table>
					<!-- END wurfl_api_info -->
					<!-- BEGIN wurfl_cloud_info -->
					<table class="medium_table no_border-top">
						<tr>
							<td>WURFL CLIENT VERSION</td>
							<td>{WURFL_CLIENT_VERSION}</td>
						</tr>
						<tr>
							<td width="150px">WURFL API VERSION</td>
							<td>{WURFL_API_VERSION}</td>
						</tr>
						<tr>
							<td>WURFL CLOUD SERVER</td>
							<td>{WURFL_CLOUD_SERVER}</td>
						</tr>
					</table>
					<!-- END wurfl_cloud_info -->
					<table class="medium_table no_border-top round-left-right-bottom">
						<tr>
							<td width="150px">jQuery Version</td>
							<td id="jqueryVersion"></td>
						</tr>
						<tr>
							<td class="round-left-bottom">jQuery UI Version</td>
							<td id="jqueryUiVersion"></td>
						</tr>
					</table>
				</div>
			</div>
		</td>
		<td class="charts_td">
			<div class="charts clearfix">
				<div class="time_activity box-shadow">
					{WIDGET_TIME_ACTIVITY}
				</div>
				<div class="opcache_insights box-shadow clearfix">
					<div class="box_header">
						OpCache Insights
					</div>
					<div class="oc_memory">
						{WIDGET_MEMORY}
					</div>
					<div class="oc_keys">
						{WIDGET_KEYS}
					</div>
					<div class="oc_hits">
						{WIDGET_HITS}
					</div>
				</div>
				<div class="user_insights box-shadow clearfix">
					<div class="box_header">
						User Insights
					</div>
					<div class="user_logins">
						{WIDGET_USER_LOGINS}
					</div>
					<div class="top_user">
						{WIDGET_TOP_USERS}
					</div>
				</div>
			</div>
		</td>
		<td class="messages_td">
			<div class="messages">
				<!-- BEGIN warnings -->
				<div class="message_error">
					<strong style="font-size: 12px; text-transform: uppercase;">{WARNING_TYPE}</strong>
					<ul style="padding: 3px 15px;">
						<!-- BEGIN warning_item -->
						<li>{WARNING_DESCRIPTION}</li>
						<!-- END warning_item -->
					</ul>
				</div>
				<!-- END warnings -->
				<div class="message_warning">
					<strong style="font-size: 12px; text-transform: uppercase;">{WARNING_TYPE}</strong>
					<ul style="padding: 3px 15px;">
						<!-- BEGIN warning_item -->
						<li>{WARNING_DESCRIPTION}</li>
						<!-- END warning_item -->
					</ul>
				</div>
				<div class="message_info">
					<strong style="font-size: 12px; text-transform: uppercase;">{WARNING_TYPE}</strong>
					<ul style="padding: 3px 15px;">
						<!-- BEGIN warning_item -->
						<li>{WARNING_DESCRIPTION}</li>
						<!-- END warning_item -->
					</ul>
				</div>
			</div>
		</td>
	</tr>
</table>