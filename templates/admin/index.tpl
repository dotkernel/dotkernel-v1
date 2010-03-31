<html>
<head>
	<title>Admin - {PAGE_TITLE}</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" >
	<meta http-equiv="Content-Style-Type" content="text/css" >
	<meta name="keywords" content="{PAGE_KEYWORDS}" >
	<meta name="description" content="{PAGE_DESCRIPTION}" >
	<link rel="stylesheet" href ="{TEMPLATES_URL}/css/admin/style.css" type="text/css" >	
	<script type="text/javascript" src="{TEMPLATES_URL}/js/admin/main.js"></script>
</head>
<body>
	<div class="wrapper">
		<div class="header_bg">
			<div class="header clearfix">
				<div class="logo"><a href="{SITE_URL}/admin/">admin panel</a></div>
				<div class="top_user_menu clearfix">
					{INFO_BAR}
				</div>
				
			</div>
			{MENU_1}
		</div>

		<div class="main">
			<div class="content">
				<div class="in_content">
					<h1>{PAGE_CONTENT_TITLE}</h1>
					{MAIN_CONTENT}
				</div>
			</div>
		</div>
		<div class="clearfooter"></div>
	</div>
	<div class="footer_bg">
		<div class="footer">
			
		</div>
	</div>
	<div class="debugger">
		{DEBUGGER}
	</div>
</body>
</html>