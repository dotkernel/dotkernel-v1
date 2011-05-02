<!-- piechart with users logins-->
<script type="text/javascript" src="{SITE_URL}/externals/jquery/jquery.flot.pie.min.js"></script>
<script type="text/javascript">
	var data = {PIEDATA};
	$(document).ready(function(){
		$.plot($("#userLoginsChart"), data, {
			series: {
				pie: { 
					show: true,
					radius: 0.8,
					highlight: {
						opacity:0.25
					}
				}
			},
			grid: {
				hoverable: true,
				clickable: true
			}
		});
	})
</script>
<fieldset style="width: 500px;float: left;margin-right: 50px;">
	<legend>Users Logins By Country</legend>
	<table cellpadding="0" cellspacing="0" class="medium_table no_padding" width="100%">
	    <tr>
	        <td>
	        	<div style="width:100%;height:300px" id="userLoginsChart"></div>
			</td>
	    </tr>
	</table>
</fieldset>