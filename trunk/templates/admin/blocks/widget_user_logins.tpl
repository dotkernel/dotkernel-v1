<!-- piechart for users logins-->
<script type="text/javascript" src="{SITE_URL}/externals/jquery/jquery.flot.pie.min.js"></script>
<script type="text/javascript">
	var userLogins = {PIEDATA};
	$(document).ready(function(){
		var total = 0;
		for (var i in userLogins){
			total += userLogins[i].data
		}
		$.map(userLogins, function(element, index){
			element.label += " (" + Math.round(element.data/total*10000) / 100 + "%)";
			return element;
		});
		$.plot($("#userLoginsChart"), userLogins, {
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