/**
 * Show Tab in admin menu
 * @param {String} id
 */
function ShowTab (id) 
{ 
   var className = document.getElementById(id).className; 
   if (className != 'selected') 
   { 
        document.getElementById(id).className = "hover_list"; 
   } 
} 
/**
 * Hide Tab in admin menu
 * @param {String} id
 */
function HideTab (id) 
{ 
   var className = document.getElementById(id).className; 
   if (className != 'selected') 
   { 
        document.getElementById(id).className = "normal"; 
   } 
} 
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
/**
 * Show/Hide submenu items
 * @param {Int} id
 */
function ShowMenuItem(id){
    for (var i = 0; i < 100; i++) 
	{
        if (document.getElementById('menu2_' + i)) 
		{
            document.getElementById('menu2_' + i).className = 'menu2';
        }
    }
    document.getElementById('menu2_' + id).className = 'menu2_selected';
}