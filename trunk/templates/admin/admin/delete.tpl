<form action="{SITE_URL}/admin/admin/delete/id/{ID}" method="post">
	<input type="hidden" name="userToken" value="{USERTOKEN}">
	<div class="box-shadow" style="width: 600px">
		<div class="box_header">
			Delete Admin Acccount: {USERNAME}
		</div>
		<ul class="form delete">
			<li class="clearfix">
				<p>Are you sure you want to delete this account ?</p>
			</li>
			<li class="clearfix">
				<input type="checkbox" name="confirm"><label>Confirm deletion</label>
				<input type="submit" class="button" value="YES" style="float: left; margin-right:10px;">
				<input type="button" onclick="window.location = '{SITE_URL}/admin/admin/list'" class="button" value="Cancel">
			</li>
		</ul>
	</div>
</form>
