<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Admin - {PAGE_TITLE}</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" >
	<meta http-equiv="Content-Style-Type" content="text/css" >
	<link rel="stylesheet" href ="{SITE_URL}/externals/dojo/tundra/tundra.css" type="text/css" >
	<link rel="stylesheet" href ="{TEMPLATES_URL}/css/admin/style.css" type="text/css" >
	<link rel="stylesheet" href ="{TEMPLATES_URL}/css/admin/{SKIN}/style.css" type="text/css" >			
	<script type="text/javascript" src="{SITE_URL}/externals/dojo/dojo.xd.js" parseOnLoad:true, isDebug:false></script>
	<script type="text/javascript" src="{TEMPLATES_URL}/js/admin/main.js"></script>
</head>
<body class="tundra">
	<div class="wrapper">
		<div class="header clearfix">
			<a href="{SITE_URL}/admin/" id="logo">{SITE_NAME}</a>
			<div class="top_user_menu clearfix">{INFO_BAR}</div>
			{MENU_1}
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