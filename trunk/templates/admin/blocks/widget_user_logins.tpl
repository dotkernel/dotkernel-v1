<!-- piechart for users logins-->
<style>
	.legendColorBox{
		vertical-align:middle;
	}
	.chartTable{
		background-color:#fff;
		border: 1px solid #CFCFCF;
		margin-top:6px;
	}
	.legendLabel{
		font-size:11px;
	}
</style>
<script type="text/javascript" src="{SITE_URL}/externals/jquery/jquery.flot.pie.min.js"></script>
<script type="text/javascript">
	var userLogins = {PIEDATA};

	$(document).ready(function(){
		pieChart("userLoginsChart", userLogins, "No users have logged in");
	})
</script>
<legend>Users Logins By Country</legend>
<table class="chart_table" width="100%">
    <tr>
        <td>
        	<div style="width:100%;height:300px" id="userLoginsChart"></div>
		</td>
    </tr>
</table>