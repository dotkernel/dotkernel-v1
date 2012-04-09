<script>
	$(document).ready(function(){
		// update jQuery and jQuery UI versions
		$("#jqueryVersion").text($().jquery);
		$("#jqueryUiVersion").text($.ui.version);
	});
</script>
<fieldset style="width: 460px;float: left;margin-right: 50px;">
	<legend>System Overview</legend>
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
			<td>APC</td>
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
	<table class="medium_table">
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
	<table class="medium_table">
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
	<table class="medium_table">
		<tr>
			<td width="150px">jQuery Version</td>
			<td id="jqueryVersion"></td>
		</tr>
		<tr>
			<td>jQuery UI Version</td>
			<td id="jqueryUiVersion"></td>
		</tr>
	</table>
</fieldset>
<fieldset style="width: 500px;float: left;margin-right: 50px;">
	{WIDGET_USER_LOGINS}
	<!-- BEGIN warnings -->
	<div class="message_error" style="width:480px;margin-top:10px;padding:10px">
		<strong style="font-size:13px">{WARNING_TYPE}</strong>
		<ul style="padding:3px 15px;color:#666;font-size:11px">
			<!-- BEGIN warning_item -->
			<li>{WARNING_DESCRIPTION}</li>
			<!-- END warning_item -->
		</ul>
	</div>
	<!-- END warnings -->
</fieldset>