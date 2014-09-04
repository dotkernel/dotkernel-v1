<!-- piechart for keys -->
<div class="opcache_title">
	OpCache Keys
</div>
<table class="chartTable">
    <tr>
        <td>
        	<div style="width:100%;height:275px" id="keysChart"></div>
		</td>
    </tr>
</table>

<script type="text/javascript">   
	var userKeys = {PIECHART_DATA};
	var colors = {PIECHART_COLOR};
	var noDataMessage = "Not installed!";
	var elementId = "keysChart";
	
	pieChart(elementId, userKeys, noDataMessage, colors);
</script>