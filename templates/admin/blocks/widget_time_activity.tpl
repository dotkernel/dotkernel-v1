<!-- linechart for last months activity-->
<legend>Last months user's logins</legend>
<table class="chartTable">
    <tr>
        <td>
        	<div style="width:100%;height:300px" id="timeActivityChart"></div>
		</td>
    </tr>
</table>

<script type="text/javascript">   
    var timeActivity = {LINECHART_DATA};
    var colors = {LINECHART_COLOR};
	var noDataMessage = "No Data";
	var elementId = "timeActivityChart";
	
	lineChart(elementId, timeActivity, noDataMessage, colors);
</script>