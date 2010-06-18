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