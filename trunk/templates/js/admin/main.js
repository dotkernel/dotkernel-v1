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

		this.live("click", function(){
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
function pieChart(elementId, data, noDataMessage){
	var total = 0;
	
	if (noDataMessage === undefined){
		noDataMessage = "No Data"
	}
	
	if (data.length === 0){
		$("#" + elementId).append($("<div style='width:100%;padding-top:100px;color:#ddd;font-size:30px;text-align:center'>"+noDataMessage+"</div>"));
		return;
	}
	
	for (var i in data){
		total += data[i].data
	}
	$.map(data, function(el, index){
		el.label += " (" + Math.round(el.data/total*10000) / 100 + "%)";
		return el;
	});
	$.plot($("#" + elementId), data, {
		series: {
			pie: { 
				show: true,
				radius: 100,
				highlight: {
					opacity:0.25
				}
			}
		},
		grid: {
			hoverable: true
		}
	});

	$("#" + elementId).bind("plothover", pieHover);

	var $legendItems = $("#" + elementId + " .legend table tbody").children();

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
}

/*** end pie chart ***/

/**
 * Show/Hide div ID
 * @param {String} id
 */
function ShowHideDiv (id)
{
	var current_status = document.getElementById(id).style.display;
	if (current_status === 'none')
	{
		document.getElementById(id).style.display = '';
	}
	else 
	{
		document.getElementById(id).style.display = 'none';
	}
}