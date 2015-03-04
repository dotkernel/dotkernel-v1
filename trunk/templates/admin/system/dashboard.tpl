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
							<td width="150px">GEOIP 1 COUNTRY LOCAL</td>
							<td>{GEOIP_COUNTRY_LOCAL}</td>
						</tr>
						<!-- BEGIN is_geoip -->
						<tr>
							<td>GEOIP 1 COUNTRY SERVER</td>
							<td>{GEOIP_COUNTRY_VERSION}</td>
						</tr>
						<!-- END is_geoip -->
					</table>
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
			<div class="system_overview security_recommendations">
				<div class="box-shadow">
					<div class="box_header">
						PHP Security Recommendations
					</div>
					<!-- BEGIN ini_value_list -->
					<table class="medium_table security_check">
						<tr>
							<td><p title="Key as in php.ini">Key</p></td>
							<td class="rightalign"><p title="Current Value"><strong>Value</strong></p></td>
							<td width="60px" class="rightalign"><p title="Recommended Value"><strong>Recommended</strong></p></td>
							<td class="rightalign" width="60px"><p title="Editable from application.ini file"><strong>Editable</strong></p></td>
						</tr>
						<!-- BEGIN ini_value  -->
						<tr>
							<td><p>{INI_KEY}</p></td>
							<td class="rightalign"><p>{CURRENT_VALUE}</p></td>
							<td class="rightalign"><p>{RECOMMENDED_VALUE}</p></td>
							<td class="rightalign" width="60px"><p>{EDITABLE}</p></td>
						</tr>
						<!-- END ini_value  -->
					</table>
					<!-- END ini_value_list -->
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
					<strong style="font-family: 'Montserrat',sans-serif; font-size: 12px; text-transform: uppercase;">{WARNING_TYPE}</strong>
					<ul style="padding: 3px 15px;">
						<!-- BEGIN warning_item -->
						<li>{WARNING_DESCRIPTION}</li>
						<!-- END warning_item -->
					</ul>
				</div>
				<!-- END warnings -->
			</div>
		</td>
	</tr>
</table>