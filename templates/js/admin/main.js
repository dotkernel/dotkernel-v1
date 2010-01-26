          function ShowTab (id) 
          { 
               var className = document.getElementById(id).className; 
               if (className != 'selected') 
               { 
                    document.getElementById(id).className = "hover_list"; 
               } 
          } 
 
          function HideTab (id) 
          { 
               var className = document.getElementById(id).className; 
               if (className != 'selected') 
               { 
                    document.getElementById(id).className = "normal"; 
               } 
          } 