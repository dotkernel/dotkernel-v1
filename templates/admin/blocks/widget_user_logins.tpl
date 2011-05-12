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
				hoverable: true
			}
		});

		$("#userLoginsChart").bind("plothover", pieHover);

		var $legendItems = $("#userLoginsChart .legend table tbody").children();

		function pieHover(event, pos, obj) 
		{
			var selectedIndex = (obj!==null ? obj.seriesIndex : null);
			if (selectedIndex === null){
				$legendItems.stop().fadeTo(100, 1);
			}else{
				$legendItems.each(function(index, element){
					if (index == selectedIndex){
						$(this).stop().fadeTo(100, 1)
					}else{
						$(this).stop().fadeTo(100, 0.5)
					}
				});
			}
		}

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