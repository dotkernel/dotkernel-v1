<script>
	function deleteFromCache(key)
	{
		var data = {'key':key, 'userToken':'{USERTOKEN}'};
		$.ajax({
			type : "POST",
			url : "{SITE_URL}/admin/system/delete-key/",
			data : data,
			success : function(){
					$("#cache_key_"+key).remove();
				}
			,
			dataType : "json"
		});
	}
	
	function clearCache()
	{
		var data = { 'userToken':'{USERTOKEN}' };
		$.ajax({
			type : "POST",
			url : "{SITE_URL}/admin/system/clear-cache/",
			data : data,
			success : function(){
					$(".cache_key").remove();
				}
			,
			dataType : "json"
		});
	}
	$(document).ready(function() {

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
	
		<!-- BEGIN cache_management -->
			<div class="system_overview security_recommendations cache_management">
				<div class="box-shadow">
					<div class="box_header">
						Dot Cache Management
					</div>

					<table class="medium_table security_check cache_key_list">
						<tr>
							<td><p title="Cache Key">Key</p></td>
							<td width="30px" class="rightalign"><p title="Time To Live"><strong>TTL</strong></p></td>
							<td width="60px" class="rightalign"><p title="Time left till expiration"><strong>Time left</strong></p></td>
							<td class="rightalign" width="70px"><p title="Delete From Cache"><strong>Action</strong></p></td>
						</tr>
						
						<!-- BEGIN cache_key  -->
						<tr id="cache_key_{CACHE_KEY_NAME}" class="cache_key">
							<td><p>{CACHE_KEY_NAME}</p></td>
							<td class="rightalign"><p title="TTL For Key: {CACHE_KEY_NAME}">{CACHE_KEY_TTL}</p></td>
							<td class="rightalign"><p title="Time left for Key: {CACHE_KEY_NAME}">{CACHE_KEY_TIME_LEFT}</p></td>
							<td class="rightalign" width="70px"><p><a class="cache_key_delete" title="Clear '{CACHE_KEY_NAME}' cache"id="cache_key_{CACHE_KEY_NAME}_delete" onclick="deleteFromCache('{CACHE_KEY_NAME}')">Clear Key</a></p></td>
						</tr>
						<!-- END cache_key  -->
						
						<!-- Full Cache Clear  -->
						<tr>
							<td colspan="4"></td>
						</tr>
						<tr class="cache_clear_all">
							<td><p>Cache</p></td>
							<td class="rightalign"><!-- <p title="Global Cache TTL">{CACHE_TTL}</p> --></td>
							<td class="rightalign"><p></p></td>
							<td class="rightalign" width="70px"><a class="cache_clear" title="Clear Cache" id="cache_key_{CACHE_KEY_NAME}" onclick="clearCache()">Clear Cache</a></td>
						</tr>
					</table>
				</div>
			</div>
		<!-- END cache_management -->
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