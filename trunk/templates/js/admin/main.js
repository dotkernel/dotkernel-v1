/*** menu ***/
// prevent pollution of the global scope, wrap everything inside a function
(function(){
	var timeout = 500,	// ms before the menu is closed
		closeTimer,			// pointer to the timer that closes the popup
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
				selectedItem = $(this).find('ul').eq(0).css('visibility', 'visible');
				// add a menuHove class to the main menu item, to keep its style
				$(this).children().first().addClass('menuHover');
			})

			.bind('mouseout',  function(){
				// when the mouse leaver the menu, wait <timeout> milliseconds
				// and then close it automatically 
				closeTimer = window.setTimeout(closeMenu, timeout);
			});
	})
	// attatch a click event to the document to close the menu
	.click(closeMenu);
})()
