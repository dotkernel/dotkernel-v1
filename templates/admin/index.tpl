<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Admin - {PAGE_TITLE}</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" >
	<meta http-equiv="Content-Style-Type" content="text/css" >
	<link rel="stylesheet" href ="{TEMPLATES_URL}/css/admin/style.css" type="text/css" >	
	<script type="text/javascript" src="{TEMPLATES_URL}/js/admin/main.js"></script>
</head>
<body>
	<div class="wrapper">
		<div class="header clearfix">
			<a href="{SITE_URL}/admin/" id="logo">ADMIN PANEL</a>
			<div class="top_user_menu clearfix">{INFO_BAR}</div>
			{MENU_1}
		</div>
		<div class="content">				
			<h1>{PAGE_CONTENT_TITLE}</h1>
				{MESSAGE_BLOCK}
					{MAIN_CONTENT}
		</div>
		<div class="debugger">
			{DEBUGGER}
		</div>
	</div>
</body>
</html>