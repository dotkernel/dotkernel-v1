<!-- columnchart for top users-->
<legend>Most active users</legend>
<table class="chartTable">
    <tr>
        <td>
        	<div style="width:100%;height:300px" id="topUsersChart"></div>
		</td>
    </tr>
</table>

<script type="text/javascript">   
    var topUsers = {COLCHART_DATA};
    var colors = {COLCHART_COLOR}; 
	var noDataMessage = "No Data";
	var elementId = "topUsersChart";
	
	columnChart(elementId, topUsers, noDataMessage, colors);
</script>