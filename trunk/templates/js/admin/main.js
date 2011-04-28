/*** menu ***/
var debug = false;		// set to true to disable menu closing
// prevent pollution of the global scope, wrap everything inside a function
(function(){
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
				if (!debug)
				{
					// when the mouse leaves the menu, wait <timeout> milliseconds
					// and then close it automatically 
					closeTimer = window.setTimeout(closeMenu, timeout);
				}
			});
	})
	// attatch a click event to the document to close the menu
	.click(closeMenu);
})()

/*** end menu ***/

/*** activate/deactivate rows ***/

/**
 * A generic function used to toggle the active state of objects in a listing (eg.: users)
 * 
 * This function will bind a click event to all elements that have the class "activeButton"
 * When the button is clicked, a XHR post request is made to "postUrl" with the paramters:
 *  - userToken - to prevent CSRF, taken from the global userTokan javascript variable
 *  - id - taken from the "id" data atribute of the clicked element
 *  - isActive - the value of the "active" data attribute is first toggled (0 becomes 1 and 1 becomes 0) before sending
 *
 * The result from the XHR request is treated as JSON and converted to a javascript object and should have the following elements:
 * 	- success - boolean
 *  - message [optional] - if success is false, this message will be shown to the user
 *  - id - the id of the object
 *  - isActive - the active status of the object
 * First result.success is checked, and if it false, the message in result.message is shown to the user.
 * Otherwise, the clicked element is found (#row_<id>) its active data attribute and its classes are updated
 * TODO:
 * 	- can be made more genereric to allow multiple such listings on the same page
 *  - better error message
 * 
 * @param {String} postUrl
 */
function enableActiveInactive(postUrl){
	$(document).ready(function(){
		$(".activeButton").click(function(){
			var data = $(this).data();
			$.post(
				postUrl,
				{
					userToken: userToken,
					id: data['id'],
					isActive: data['active']*(-1) + 1	//toggle between 0 and 1
				},
				function(result){
					if (result['success']){
						var $targetElement = $('#row_'+result['id']);

						$targetElement.data('active', result['isActive']);

						if (result['isActive'] === 1){
							$targetElement.removeClass('inactive_state').addClass('active_state');
						}else{
							$targetElement.removeClass('active_state').addClass('inactive_state');
						}
					}else{
						// todo: use something better
						alert(result['message']);
					}
				},
				"json"
			);
		});
	});
};

/*** end activate/deactivate rows ***/

/**
 * Show/Hide div ID
 * @param {String} id
 */
function ShowHideDiv (id)
{
	var current_status = document.getElementById(id).style.display;
	if (current_status == 'none')
	{
		document.getElementById(id).style.display = '';
	}
	else 
	{
		document.getElementById(id).style.display = 'none';
	}
}