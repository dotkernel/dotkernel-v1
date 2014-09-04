<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<link href="http://fonts.googleapis.com/css?family=Cabin:bold" rel="stylesheet" type="text/css" >
		<title>Admin - {PAGE_TITLE}</title>
		<link rel="apple-touch-icon" href="{SITE_URL}/images/apple-touch-icon.png">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link href='http://fonts.googleapis.com/css?family=Montserrat:700' rel='stylesheet' type='text/css'>
		
		<link href='http://fonts.googleapis.com/css?family=Raleway:400,700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Asap:400,700' rel='stylesheet' type='text/css'>
		
		<link rel="stylesheet" href ="{SITE_URL}/externals/jquery/ui/jquery-ui.min.css" type="text/css" >
		<link rel="stylesheet" href ="{TEMPLATES_URL}/css/admin/style.css" type="text/css" >
		<link rel="stylesheet" href ="{TEMPLATES_URL}/css/admin/{SKIN}/style.css" type="text/css" >
		<script src="{SITE_URL}/externals/jquery/jquery.min.js"></script>
		<script src="{SITE_URL}/externals/jquery/ui/jquery-ui.min.js"></script>
		<script src="{SITE_URL}/externals/highcharts/highcharts.js"></script>
		<script src="{TEMPLATES_URL}/js/admin/main.js"></script>
	</head>
	<body> 
		<div class="wrapper {LOGIN_CLASS}">
			<div class="header">
				<div class="container clearfix">
					<a href="{SITE_URL}/admin/" id="logo">{SITE_NAME}</a>
					{MENU}
					<div class="top_user_menu clearfix">{INFO_BAR}</div>
				</div>
			</div>
			{BREADCRUMBS}
			<div class="content clearfix">
				<div class="container">
					<h1>{PAGE_CONTENT_TITLE}</h1>
					{MESSAGE_BLOCK}
					{MAIN_CONTENT}
				</div>
			</div>
		</div>
		<div class="footer">
			<div class="debugger">
				{DEBUGGER}
			</div>
		</div>
	</body>
</html>