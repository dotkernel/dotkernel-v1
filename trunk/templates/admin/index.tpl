<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<link  href="http://fonts.googleapis.com/css?family=Cabin:bold" rel="stylesheet" type="text/css" >
	<title>Admin - {PAGE_TITLE}</title>
	<link rel="stylesheet" href ="{SITE_URL}/externals/jquery/jquery-ui-1.8.12.min.css" type="text/css" >
	<link rel="stylesheet" href ="{TEMPLATES_URL}/css/admin/style.css" type="text/css" >
	<link rel="stylesheet" href ="{TEMPLATES_URL}/css/admin/{SKIN}/style.css" type="text/css" >			
	<script src="{SITE_URL}/externals/jquery/jquery-1.6.min.js"></script>
	<script src="{SITE_URL}/externals/jquery/jquery-ui-1.8.12.min.js"></script>
	<script src="{TEMPLATES_URL}/js/admin/main.js"></script>
</head>
<body>
	<div class="wrapper">
		<div class="header clearfix">
			<a href="{SITE_URL}/admin/" id="logo">{SITE_NAME}</a>
			<div class="top_user_menu clearfix">{INFO_BAR}</div>
			{MENU}
		</div>
		<div class="content clearfix">
			<h1>{PAGE_CONTENT_TITLE}</h1>
				{MESSAGE_BLOCK}
					{MAIN_CONTENT}
		</div>
	</div>
	<div class="footer">
		<div class="debugger">
			{DEBUGGER}
		</div>
	</div>
</body>
</html>