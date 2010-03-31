<span style="color: #ff0000;">{ERROR}</span>
	<div class="login_big">
		<form action="{SITE_URL}/user/authorize" method="post" >
			<input type="hidden" name="send" value="on">
			<ul class="g_form">
				<li class="clearfix positioned">
					<p class="contact_label">Username</p>
					<input type="text" value="" name="username">
				</li>
				<li class="clearfix positioned">
					<p class="contact_label">Password</p>
					<input type="text" value="" name="password">
				</li>	
				<li class="clearfix positioned">
					<p class="contact_label">&nbsp;</p>
					<input type="submit" onclick="" class="btn" value="login">
				</li>
				<li class="clearfix positioned">
					<p class="contact_label">&nbsp;</p>
					<a href="{SITE_URL}/user/forgot-password">Password Recovery</a>
				</li>
			</ul>
		</form>
		
	</div>
