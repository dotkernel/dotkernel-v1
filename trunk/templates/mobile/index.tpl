<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>{PAGE_TITLE}</title>
	<link rel="apple-touch-icon" href="{SITE_URL}/images/apple-touch-icon.png">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta name="keywords" content="{PAGE_KEYWORDS}" >
	<meta name="description" content="{PAGE_DESCRIPTION}" >
	<script src="{SITE_URL}/externals/jquery/jquery.min.js"></script>
	<link rel="stylesheet" href="{SITE_URL}/externals/jquery/mobile/jquery.mobile.min.css" />
	<!-- for PHP redirect to work properly, add this code here 
	set ajaxFormsEnabled to false -->
	<script type="text/javascript">
		$(document).bind("mobileinit", function(){
		$.extend(  $.mobile , {
				ajaxFormsEnabled: false
			});
		});

        </script>
	<!-- end PHP redirect-->
	<script src="{SITE_URL}/externals/jquery/mobile/jquery.mobile.min.js"></script>
	<script type="text/javascript" src="{TEMPLATES_URL}/js/mobile/main.js"></script>
</head>
<body>
<div data-role="page"> 
	<div data-role="header"> 
		<p>&nbsp;</p>
		<a data-role="button" data-inline="true" href="{SITE_URL}" rel="external" style="margin-top:0.3em;" >Web {SITE_NAME}</a>
	</div>
	<!-- /header -->	
	<div data-role="content" class="ui-content">
		<h1>{PAGE_CONTENT_TITLE}</h1>
		<h2>{MESSAGE_BLOCK}</h2>
		{MAIN_CONTENT}
	</div>
	<!-- /content -->
	<div data-role="footer">
		&copy; 2009 - 2015 DotBoost Technologies Inc.
	</div>
	<!-- /footer -->
</div>
</body>
</html>