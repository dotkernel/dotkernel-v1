<script type="text/javascript" src="{SITE_URL}/externals/google.chart.js"></script>
<script type="text/javascript">   
    var userLogins = {PIEDATA};
	var noDataMessage = "No Data";
	var elementId = "userLoginsChart";
	
	pieChart(elementId, userLogins, noDataMessage);
</script>

<!-- piechart for users logins-->
<style>
	.chartTable{
		background-color:#fff;
		border: 1px solid #CFCFCF;
		margin-top:6px;
		border-spacing: 0px;
	}
</style>

<legend>Users Logins By Country</legend>
<table class="chartTable">
    <tr>
        <td>
        	<div style="width:100%;height:300px" id="userLoginsChart"></div>
		</td>
    </tr>
</table>