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