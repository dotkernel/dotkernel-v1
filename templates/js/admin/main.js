/*** menu ***/
$(document).ready(function(){
	var timeout = 500,	// ms before the menu is closed
		closeTimer,			// timer that closes the popup
		selectedItem;		// the popup that is currently being shown

	// hide the submenu	
	function closeMenu(){
		if(selectedItem){
			selectedItem.css('visibility', 'hidden');
			selectedItem.parent().children().first().removeClass('menuHover');
		}
	}

	// bind event to document.ready
	$(document).ready(function()
	{
		// bind mouseover and mouseout events to all items of the main menu
		$('#mainMenu > li')
			.bind('mouseover', function(){
				// clear the close timer if it's set
				if (closeTimer){
					window.clearTimeout(closeTimer);
					closeTimer = null;
				}
				// close any other menu that might already be shown
				closeMenu();
				// show the submenu
				selectedItem = $(this).find('ul').css('visibility', 'visible');
				$(this).children().first().addClass('menuHover');
			})

			.bind('mouseout',  function(){
				closeTimer = window.setTimeout(closeMenu, timeout);
			});
	})
	// attatch a click event to the document to close the menu
	.click(closeMenu);
});

/*** end menu ***/

/*** activate/deactivate rows ***/

/**
 * A jQuery plugin used to toggle the active state of objects in a listing (eg.: users)
 * 
 * This plugin will bind a click event to all elements passed to it by the jQuery selector
 * When the button is clicked, a XHR post request is made to "targetUrl" with the paramters:
 *  - userToken - to prevent CSRF, taken from the global userTokan javascript variable
 *  - id - taken from the "id" data atribute of the clicked element
 *  - isActive - the value of the "active" data attribute is first toggled (0 becomes 1 and 1 becomes 0) before sending
 *
 * The result from the XHR request is treated as JSON and converted to a javascript object and should have the following elements:
 *  - success - boolean
 *  - message [optional] - if success is false, this message will be shown to the user
 *  - id - the id of the object
 *  - isActive - the active status of the object
 * First result.success is checked, and if it is false, the message in result.message is shown to the user using a jQUery UI dialog
 * Otherwise, the clicked element's active data attribute and its classes are updated
 * 
 * Usage:
 *   $(selector).activeFlags(options)
 * 
 * Options:
 *   targetUrl - the url to which a POST request is made
 *   classActive, classInactive - the classes for the different button states. Default to active_state and inactive_state
 *   onError - the error handler function. By default, a dialog will be shown using jQuery UI
 * @param {String} postUrl
 */

(function($){
	$.fn.activeFlags = function( options ) {
		var settings = {
			targetUrl : null,
			classActive : 'active_state',
			classInactive : 'inactive_state',
			onError : function(message){
				$('<div title="Error"><br/><br/>' + message + '</div>').dialog({
					modal: true
				});
			}
		};
		$.extend(settings, options);
		if (settings.targetUrl === null){
			return;
		}

		this.on("click", function(){
			var $targetElement = $(this),
				data = $targetElement.data();
			$.post(
				settings.targetUrl,
				{
					userToken: userToken,
					id: data.id,
					isActive: data.active*(-1) + 1	//toggle between 0 and 1
				},
				function(result){
					if (result.success){
						$targetElement.data('active', result.isActive);
						if (result.isActive === 1){
							$targetElement.removeClass(settings.classInactive).addClass(settings.classActive);
						}else{
							$targetElement.removeClass(settings.classActive).addClass(settings.classInactive);
						}
					}else{
						settings.onError(result.message);
					}
				},
				"json"
			);
		});

		return this;
	};
}(jQuery));

/*** end activate/deactivate rows ***/

/*** pie chart ***/
function pieChart(elementId, userLogins, noDataMessage, colors){
	
	var arrayTableData = [];
	
	if (userLogins.length === 0)
	{
		$("#" + elementId).append($("<div style='width:100%;padding-top:100px;color:#ddd;font-size:30px;text-align:center'>"+noDataMessage+"</div>"));
		return;
	}
	
	//prepare array
	for (var i in userLogins)
	{
		arrayTableData[i] = { name: userLogins[i].label, y: userLogins[i].data, id: userLogins[i].data, color: colors[i] };
	}
	
	chart = new Highcharts.Chart({
		chart: {
			renderTo: elementId,
			plotBackgroundColor: null,
			plotBorderWidth: 0,
		},
		credits: {
			enabled: false
		},
		title: {
			text: ''
		},
		tooltip: {
			formatter: function() {
				return '<b>'+ this.point.name +'</b>: '+ this.percentage.toPrecision(3) +' %';
			}
		},
		legend: {
			width: 100,
			layout: "vertical",
			align: "center",
			verticalAlign: "bottom",
			borderWidth: 0,
			labelFormatter: function() {
				return this.name;
			}
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: false
				},
				showInLegend: true,
				point: {
					events: {
						legendItemClick: function() {
							if(this.y == 0)
								this.update(this.id);
							else
								this.update(0);
						}
					}
				}
			}
		},
		series: [{
			type: 'pie',
			name: 'User logins',
			data: arrayTableData
		}]
	});
	
}
/*** end pie chart ***/

/*** column chart ***/
function columnChart(elementId, topUsers, noDataMessage, colors){
	
	var labelData = [];
	var valueData = [];
	
	if (topUsers.length === 0)
	{
		$("#" + elementId).append($("<div style='width:100%;padding-top:100px;color:#ddd;font-size:30px;text-align:center'>"+noDataMessage+"</div>"));
		return;
	}
	
	//prepare array
	for (var i in topUsers)
	{
		labelData[i] = topUsers[i].label;
		valueData[i] = { y: topUsers[i].data, color: colors[i]};
	}
	
	chart = new Highcharts.Chart({
		chart: {
			renderTo: elementId,
			plotBackgroundColor: null,
			plotBorderWidth: 0,
		},
		credits: {
			enabled: false
		},
		title: {
			text: ''
		},
		yAxis: {
			title: {
				text: 'Logins count'
			}
		},
		xAxis: {
			categories: labelData,
			labels: {
				rotation: -45,
				align: 'right',
				style: {
					font: 'normal 11px Verdana, sans-serif'
				}
			}
		},
		tooltip: {
			formatter: function() {
				return '<b>'+ this.x + '</b><br/>' + 'Logins count: '+ this.y;
			}
		},
		plotOptions: {
			column: {
				dataLabels: {
					enabled: true
				},
				showInLegend: false
			}
		},
		series: [{
			type: 'column',
			name: 'Top Users',
			data: valueData,
			dataLabels: {
				enabled: true,
				rotation: -90,
				color: '#FFFFFF',
				align: 'right',
				x: 2,
				y: 5
			}
		}]
	});
}
/*** end of column chart ***/

/*** line chart ***/
function lineChart(elementId, timeActivity, noDataMessage, colors){
	if (timeActivity.length === 0)
	{
		$("#" + elementId).append($("<div style='width:100%;padding-top:100px;color:#ddd;font-size:30px;text-align:center'>"+noDataMessage+"</div>"));
		return;
	}
	
	chart = new Highcharts.Chart({
		chart: {
			renderTo: elementId,
			type: 'line',
			plotBackgroundColor: null,
			plotBorderWidth: 0,
		},
		credits: {
			enabled: false
		},
		title: {
			text: ''
		},
		colors: colors,
		tooltip: {
			formatter: function() {
					return '<b>' + this.series.name + ' ' + this.x + '</b><br/>' + 'Total logins: ' + this.y;
			}
		},
		yAxis: {
			title: {
				text: 'Logins count'
			},
			min: 0
		},
		xAxis: {
			categories: ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'],
			labels: {
				rotation: -45,
				align: 'right',
				style: {
					font: 'normal 10px Verdana, sans-serif'
				}
			}
		},
		series: timeActivity
	});
}
/*** end of column chart ***/

/**
 * Show/Hide div ID
 * @param {String} id
 */
function ShowHideDiv (idv, classv)
{
	if ($('#' + idv).is(':visible'))
	{
		$('#' + idv).hide();
	}
	else
	{
		$('.' + classv).hide();
		$('#' + idv).show();
	}
}