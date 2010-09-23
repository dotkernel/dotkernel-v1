<fieldset style="width: 500px;">
	<legend>System Overview</legend>
	<table cellpadding="0" cellspacing="0" class="big_table" width="100%">
		<tr>
			<td valign="top" width="30%" class="row2"><b>SQL VERSION</b></td>
			<td class="row1"  width="70%">MYSQL {MYSQL}</td>
		</tr>
		<tr>
			<td valign="top"  class="row2"><b>PHP Version</b></td>
			<td class="row1">{PHP} ({PHPAPI}) &nbsp;&nbsp;[ <a href="{SITE_URL}/admin/system/phpinfo">Php Info</a> ]</td>
		</tr>				
		<tr>
			<td valign="top" class="row2"><b>ZF Version</b></td>
			<td class="row1"> {ZFVERSION}</td>
		</tr>
	</table>
</fieldset>
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