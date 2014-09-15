<form action="{SITE_URL}/admin/user/send-password/id/{ID}" method="post" >
<input type="hidden" name="userToken" value="{USERTOKEN}">
	<div class="rounded-corners box-shadow" style="width: 600px">
		<div class="box_header round-left-right-top">
			Sent User Acccount Password To: {USERNAME}
		</div>
		<ul class="form delete">
			<li class="clearfix">
				<p>Are you sure you want to send the password to this account?</p>
			</li>
			<li class="clearfix">
				<input type="checkbox" name="confirm"><label>Confirm</label>
				<input type="submit" class="button" value="YES" style="float: left; margin-right:10px;">
				<input type="button" onclick="window.location = '{SITE_URL}/admin/user/list'" class="button" value="Cancel">
			</li>
		</ul>
	</div>
</form>
