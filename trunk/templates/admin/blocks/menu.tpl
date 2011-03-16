<ul id="mainMenu">
	<!-- BEGIN menu_list -->
		<li>
			<a href="{MENU_LINK}" title="{MENU_DESCRIPTION}">{MENU_TITLE}</a>
			<ul>
				<!-- BEGIN submenu_list -->
					<li><a href="{SUBMENU_LINK}" title="{MENU_DESCRIPTION}">{SUBMENU_TITLE}</a></li>
				<!-- END submenu_list -->				
			</ul>
		</li>
	<!-- END menu_list -->
</ul>
<div class="clearfix"></div>
<ul id="breadcrumbs">
	<li><a href="#">Dashboard</a></li>
	<li><img src="{SITE_URL}/templates/css/admin/images/breadcrumb_arrow.png"/></li>
	<li><a href="#">Home</a></li>
</ul>
<script>
/*** menu ***/
(function(){
	var timeout = 500,	// ms before the menu is closed
		closetimer = 0,
		ddmenuitem = 0;

	function closeMenu(){
		if(ddmenuitem){
			ddmenuitem.css('visibility', 'hidden');
			ddmenuitem.parent().children().first().removeClass('menuHover');
		}
	}

	function cancelTimer(){
		if (closetimer){
			window.clearTimeout(closetimer);
			closetimer = null;
		}
	}

	$(document).ready(function()
	{
		$('#mainMenu > li')
			.bind('mouseover', function(){
				cancelTimer();
				closeMenu();
				ddmenuitem = $(this).find('ul').eq(0).css('visibility', 'visible');
				$(this).children().first().addClass('menuHover');
			})
			.bind('mouseout',  function(){
				closetimer = window.setTimeout(closeMenu, timeout);
			});
	}).click(closeMenu);
})()
</script>

