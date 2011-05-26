<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=9">
	<title>{PAGE_TITLE}</title>
	<meta name="keywords" content="{PAGE_KEYWORDS}" >
	<meta name="description" content="{PAGE_DESCRIPTION}" >
	<link rel="canonical" href="{CANONICAL_URL}" >
	<link rel="stylesheet" href ="{TEMPLATES_URL}/css/frontend/style.css" type="text/css" >
	<link rel="stylesheet" href ="{SITE_URL}/externals/fonts/stylesheet.css" type="text/css" >	
	<script src="{SITE_URL}/externals/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="{TEMPLATES_URL}/js/frontend/main.js"></script>
</head>
<body>
	<header>
		<div id="logo">
			<h1><a href="{SITE_URL}/">{SITE_NAME}</a></h1>
		</div>
		{MENU_1}
	</header>
	<div id="wrapper">
		<div id="body">
			<nav id="sidebar">
				<div class="left_head">
					Menu
				</div>
				{MENU_2}
			</nav>
			<div id="content">
				<h1>{PAGE_CONTENT_TITLE}</h1>
				{MESSAGE_BLOCK}
				{MAIN_CONTENT}
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<footer>
		{MENU_3}
		<div class="debugger">
			{DEBUGGER}
		</div>
	</footer>
</body>
</html>