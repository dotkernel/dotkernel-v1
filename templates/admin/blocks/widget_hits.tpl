<!-- piechart for hits -->
<div class="opcache_title">
	OpCache Hits
</div>
<table class="chartTable">
    <tr>
        <td>
        	<div style="width:100%; height:275px" id="hitsChart"></div>
		</td>
    </tr>
</table>

<script type="text/javascript">   
    var userHits = {PIECHART_DATA};
    var colors = {PIECHART_COLOR};
	var noDataMessage = "Not installed!";
	var elementId = "hitsChart";
	
	pieChart(elementId, userHits, noDataMessage, colors);
</script>