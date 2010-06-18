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