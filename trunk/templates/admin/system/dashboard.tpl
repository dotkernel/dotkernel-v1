<fieldset style="width: 400px;float: left;margin-right: 50px;">
	<legend>System Overview</legend>
	<table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
		<tr>
			<td class="row2" width="120px"><b>MYSQL</b></td>
			<td class="row1">MYSQL {MYSQL}</td>
		</tr>
		<tr>
			<td class="row2"><b>PHP</b></td>
			<td class="row1">{PHP} ({PHPAPI}) &nbsp;&nbsp;[ <a href="{SITE_URL}/admin/system/phpinfo">Php Info</a> ]</td>
		</tr>				
		<tr>
			<td class="row2 last_td"><b>Zend Framework</b></td>
			<td class="row1 last_td"> {ZFVERSION}</td>
		</tr>
	</table>
	<table cellpadding="0" cellspacing="0" class="medium_table" width="100%">
		<!-- BEGIN is_geoip -->
	 	<tr>
			<td class="row2" width="120px"><b>GEOIP CITY</b></td>
			<td class="row1"> {GEOIP_CITY_VERSION}</td>
		</tr>
				<tr>
			<td class="row2 last_td"><b>GEOIP COUNTRY</b></td>
			<td class="row1 last_td"> {GEOIP_COUNTRY_VERSION}</td>
		</tr>
		<!-- END is_geoip -->
	</table>
</fieldset>
{WIDGET_USER_LOGINS}
