<form action="{SITE_URL}/admin/admin/add" method="post" >
	<input type="hidden" name="userToken" value="{USERTOKEN}">
	<div class="box-shadow" style="width: 500px">
		<div class="box_header">
			Add New Admin
		</div>
		<ul class="form">
			<li class="clearfix">
				<label>Username<span class="required">*</span></label>
				<input type="text" name="username" value="{USERNAME}">
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
				<label>Active</label>
				<div class="radios">
					<label for="active1">Yes</label> <input type="radio" id="active1" name="isActive" value="1" style="height: auto;" {ACTIVE_1}> 
					<label for="active0">No</label>	<input type="radio" id="active0" name="isActive" value="0" style="height: auto;" {ACTIVE_0}>
				</div>
			</li>
			<li class="clearfix">
				<label>&nbsp;</label>
				<input type="submit" class="button" value="add">
			</li>
		</ul>
	</div>
</form>
