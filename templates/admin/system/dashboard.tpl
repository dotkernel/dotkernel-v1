<fieldset style="width: 460px;float: left;margin-right: 50px;">
	<legend>System Overview</legend>
	<!-- BEGIN warnings_table -->
	<table cellpadding="0" cellspacing="0" class="medium_table warnings" width="100%">
		<!-- BEGIN warnings_list -->
		<tr>
			<td class="row2{TD_CLASS}" width="150px"><b>{WARNING_TYPE}</b></td>
			<td class="row1{TD_CLASS}"><b>{WARNING_DESCRIPTION}</b></td>
		</tr>
		<!-- END warnings_list -->
	</table>
	<!-- END warnings_table -->
	<table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
		<tr>
			<td class="row2" width="150px"><b>HOSTNAME</b></td>
			<td class="row1"><b>{HOSTNAME}</b></td>
		</tr>
		<tr>
			<td class="row2"><b>PHP</b></td>
			<td class="row1">{PHP} ({PHPAPI}) &nbsp;&nbsp;[ <a href="{SITE_URL}/admin/system/phpinfo">PHP Info</a> ]</td>
		</tr>	
		<tr>
			<td class="row2"><b>MYSQL</b></td>
			<td class="row1">MYSQL {MYSQL}</td>
		</tr>			
		<tr>
			<td class="row2 last_td"><b>Zend Framework</b></td>
			<td class="row1 last_td"> {ZFVERSION}</td>
		</tr>
	</table>
	<table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
		<tr>
			<td class="row2"  width="150px"><b>GEOIP COUNTRY LOCAL</b></td>
			<td class="row1"> {GEOIP_COUNTRY_LOCAL}</td>
		</tr>
		<!-- BEGIN is_geoip -->
	 	<tr>
			<td class="row2"><b>GEOIP CITY</b></td>
			<td class="row1"> {GEOIP_CITY_VERSION}</td>
		</tr>
		<tr>
			<td class="row2 last_td"><b>GEOIP COUNTRY</b></td>
			<td class="row1 last_td"> {GEOIP_COUNTRY_VERSION}</td>
		</tr>
		<!-- END is_geoip -->
	</table>
	<table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
		<tr>
			<td class="row2" width="150px"><b>WURFL Cache Built</b></td>
			<td class="row1">{WURFLCACHEBUILT} [ <a href="{SITE_URL}/admin/system/build-wurfl-cache">build now</a> ]</td>
		</tr>
		<tr>
			<td class="row2 last_td"><b>WURFL Date</b></td>
			<td class="row1 last_td">{WURFLDATE}</td>
		</tr>
	</table>
</fieldset>
{WIDGET_USER_LOGINS}
