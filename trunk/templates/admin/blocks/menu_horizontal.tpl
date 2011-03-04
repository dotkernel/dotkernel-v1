<div id="menuContainer">
<div class="menu_1">
<!-- BEGIN top_menu -->
	<ul class="clearfix">
		<!-- BEGIN top_menu_item -->
			<li class="normal{TOP_MENU_SEL}" id="top_{TOP_MENU_ID}">
				<!-- BEGIN top_normal_menu_item -->
					<a href="javascript:ShowMenuItem('{TOP_MENU_ID}');" title="{TOP_MENU_DESCRIPTION}" 
					onmouseover="ShowMenuItem('{TOP_MENU_ID}');">{TOP_MENU_TITLE}</a>
				<!-- END top_normal_menu_item -->
			</li>
		<!-- END top_menu_item -->
	</ul>
</div>
<!-- BEGIN top_sub_menu_item -->
<div class="menu2{TOP_MENU_SEL}" id="menu2_{TOP_MENU_ID}">
	<ul class="clearfix">
		<!-- BEGIN top_normal_sub_menu_item -->
			<li class="normal{TOP_SUB_MENU_SEL}"><a href="{TOP_SUB_MENU_LINK}" title="{TOP_SUB_MENU_DESCRIPTION}">{TOP_SUB_MENU_TITLE}</a></li>
		<!-- END top_normal_sub_menu_item -->
	</ul>
</div>
<!-- END top_sub_menu_item -->
<!-- END top_menu -->
</div>
<script>
	var currentMenuId = '{CURRENT_MENU_ID}';
	// return the menu to the initial position when the mouse
	// leaves the menu area
	dojo.connect(dojo.byId('menuContainer'), 'onmouseleave', function(){
		ShowMenuItem('{CURRENT_MENU_ID}', '1');
	})
</script>

