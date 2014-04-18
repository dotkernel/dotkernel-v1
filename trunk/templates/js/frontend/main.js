/**
 * Display menu in frontend
 * @param {String} menu
 * @param {String} parent_id
 */
function ShowMenuItem(menu, parent_id)
{
	for (var i = 0; i <= 100; i++)
	{
		if (document.getElementById(menu + '_' + i))
		{
			document.getElementById(menu + '_' + i).style.display = 'none';
		}
	}

	if (document.getElementById(menu + '_' + parent_id))
	{
		document.getElementById(menu + '_' + parent_id).style.display = 'block';
	}
}
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

/**
 * Show/Hide top menu
 * @param void
 */
function ShowTopMenu ()
{
	if ($('#top_menu').is(':visible'))
	{
		$('#top_menu').slideUp("slow", function() {
			
		});
	}
	else
	{
		$('#top_menu').slideDown("slow", function() {
			
		});
	}
}