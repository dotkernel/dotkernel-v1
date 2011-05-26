<!-- BEGIN top_menu -->
<div class="vertical_menu1">
	<ul>
		<!-- BEGIN top_menu_item -->
			<li class="top_menu{TOP_MENU_SEL}">
				<!-- BEGIN top_normal_menu_item -->
					<a href="{TOP_MENU_LINK}" target="{TOP_MENU_TARGET}">{TOP_MENU_TITLE}</a>
				<!-- END top_normal_menu_item -->
				<!-- BEGIN top_parent_menu_item -->
					<a href="javascript:ShowMenuItem('top_sub_menu', '{TOP_MENU_ID}');">{TOP_MENU_TITLE}</a>
				<!-- END top_parent_menu_item -->
			</li>
					<!-- BEGIN top_sub_menu -->
						<!-- BEGIN top_sub_menu_item -->
							<li class="top_sub_menu_item{TOP_SUB_MENU_ITEM_SEL}"><a href="{TOP_SUB_MENU_LINK}" target="{TOP_SUB_MENU_TARGET}">{TOP_SUB_MENU_TITLE}</a></li>
						<!-- END top_sub_menu_item -->
					<!-- END top_sub_menu -->
		<!-- END top_menu_item -->
	</ul>
</div>
<!-- END top_menu -->