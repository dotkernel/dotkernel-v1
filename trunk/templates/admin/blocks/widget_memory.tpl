<!-- piechart for memory usage -->
<legend>OpCache Memory</legend>
<table class="chartTable">
	<tr>
		<td>
			<div style="width:100%;height:275px" id="memoryChart"></div>
		</td>
	</tr>
</table>

<script type="text/javascript">   
    var userMemory = {PIECHART_DATA};
    var colors = {PIECHART_COLOR};
	var noDataMessage = "Not installed!";
	var elementId = "memoryChart";
	
	pieChart(elementId, userMemory, noDataMessage, colors);
</script>