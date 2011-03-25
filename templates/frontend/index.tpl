<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<link  href="http://fonts.googleapis.com/css?family=Cabin:bold" rel="stylesheet" type="text/css" >
	<title>{PAGE_TITLE}</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" >
	<meta http-equiv="Content-Style-Type" content="text/css" >
	<meta name="keywords" content="{PAGE_KEYWORDS}" >
	<meta name="description" content="{PAGE_DESCRIPTION}" >
	<link rel="canonical" href="{CANONICAL_URL}" >
	<link rel="stylesheet" href ="{TEMPLATES_URL}/css/frontend/style.css" type="text/css" >	
	<script type="text/javascript" src="{SITE_URL}/externals/dojo/dojo.xd.js" parseOnLoad:true, isDebug:false></script>
	<script type="text/javascript" src="{TEMPLATES_URL}/js/frontend/main.js"></script>
</head>
<body>
	<div class="header_bg">
		<div class="header">
			<div class="logo">
				<h1><a href="{SITE_URL}/"><i><b>{SITE_NAME}</b></i></a></h1></div>
			{MENU_1}
		</div>
	</div>
	<div class="main_bg">
			<div class="main clearfix">
				<div class="left_bar">
					<p class="left_head">Menu</p>
					{MENU_2}
					{LOGIN_BOX}
				</div>
				<div class="content">
					<h1>{PAGE_CONTENT_TITLE}</h1>
						{MESSAGE_BLOCK}
					{MAIN_CONTENT}
				</div>
			</div>
	</div>
	<div class="footer_bg">
		<div class="footer">
			{MENU_3}
			<div class="debugger">
				{DEBUGGER}
			</div>
		</div>		
	</div>
</body>
</html>