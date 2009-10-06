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
			<li>			
				<div class="vertical_menu2">
					<!-- BEGIN top_sub_menu -->
					<ul class="top_sub_menu{TOP_SUB_MENU_SEL}" id="top_sub_menu_{TOP_MENU_ID}">
						<!-- BEGIN top_sub_menu_item -->
							<li class="top_sub_menu_item{TOP_SUB_MENU_ITEM_SEL}"><a href="{TOP_SUB_MENU_LINK}" target="{TOP_SUB_MENU_TARGET}">{TOP_SUB_MENU_TITLE}</a></li>
						<!-- END top_sub_menu_item -->
					</ul>
					<!-- END top_sub_menu -->
				</div>
			</li>
		<!-- END top_menu_item -->
	</ul>
</div>
<!-- END top_menu -->