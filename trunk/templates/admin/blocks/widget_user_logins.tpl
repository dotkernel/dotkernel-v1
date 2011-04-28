<!-- piechart with users logins-->
<script type="text/javascript" src="{SITE_URL}/templates/js/admin/system.js"></script>
 <script type="text/javascript">
 	pieChart({PIEDATA}); 
 </script>
<fieldset style="width: 500px;float: left;margin-right: 50px;">
	<legend>Users Logins By Country</legend>
	<table cellpadding="0" cellspacing="0" class="medium_table no_padding" width="100%">
	    <tr>
	        <td>
	        	<div id="chartCountryUserLogin" style="width: 300px; height: 300px; float: left; margin: 0px 0px 0px 10px;">
			    </div>
			</td>
			<td>
				<div id="chartCountryLegend">		
				</div>
	        </td>
	    </tr>
	</table>
</fieldset>