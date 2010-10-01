<fieldset style="width: 500px;">
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
			<td class="row2"><b>Zend Framework</b></td>
			<td class="row1"> {ZFVERSION}</td>
		</tr>
		<!-- BEGIN is_geoip -->
	 	<tr>
			<td class="row2"><b>GEOIP CITY</b></td>
			<td class="row1"> {GEOIP_CITY_VERSION}</td>
		</tr>
				<tr>
			<td class="row2"><b>GEOIP COUNTRY</b></td>
			<td class="row1"> {GEOIP_COUNTRY_VERSION}</td>
		</tr>
		<!-- END is_geoip -->
	</table>
</fieldset>

<!-- piechart with users logins-->
<script type="text/javascript" src="{SITE_URL}/externals/dojo/dojo.xd.js"></script>
<script type="text/javascript" src="{SITE_URL}/templates/js/admin/system.js"></script>
<style type="text/css">@import "{TEMPLATES_URL}/css/admin/dojo.css";</style>
 <script type="text/javascript">
 	pieChart({PIEDATA}); 
 </script>
<fieldset style="width: 500px;">
	<legend>Users Logins By Country</legend>
		 <div id="chartCountryUserLogin" style="width: 300px; height: 300px; float: left;">
	     </div>
		<div id="chartCountryLegend" >		
		</div>
</fieldset>