<form action="{SITE_URL}/admin/admin/update/id/{ID}" method="post" >
	<input type="hidden" name="userToken" value="{USERTOKEN}">
	<div class="box-shadow" style="width: 500px">
		<div class="box_header">
			Admin Acccount
		</div>
		<ul class="form">
			<li class="clearfix">
				<label>Username<span class="required">*</span></label>
				<p class="username">{USERNAME}</p>
			</li>
			<li class="clearfix">
				<label>Password<span class="required">*</span></label>
				<input type="password" name="password" value="{PASSWORD}" >
			</li>
			<li class="clearfix">
				<label>Confirm Password<span class="required">*</span></label>
				<input type="password" name="password2" value="{PASSWORD}" >
			</li>
			<li class="clearfix">
				<label>Email<span class="required">*</span></label>
				<input type="text" name="email" value="{EMAIL}" >
			</li>
			<li class="clearfix">
				<label>First Name</label>
				<input type="text" name="firstName" value="{FIRSTNAME}" >
			</li>
			<li class="clearfix">
				<label>Last Name</label>
				<input type="text" name="lastName" value="{LASTNAME}" >
			</li>
			<li class="clearfix">
				<label>&nbsp;</label>
				<input type="submit" class="button" value="update">
			</li>
		</ul>
	</div>
</form>
