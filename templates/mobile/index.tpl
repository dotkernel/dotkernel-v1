<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.1//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{PAGE_TITLE}</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" >
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta name="keywords" content="{PAGE_KEYWORDS}" >
	<meta name="description" content="{PAGE_DESCRIPTION}" >
	<link rel="stylesheet" href="{SITE_URL}/externals/jquery/jquery.mobile-1.0a1.min.css" />
	<script src="{SITE_URL}/externals/jquery/jquery-1.4.3.min.js"></script>
	<script src="{SITE_URL}/externals/jquery/jquery.mobile-1.0a1.min.js"></script>
</head>
<body>
<div data-role="page" data-theme="e"> 
	<div data-role="header" data-theme="b">
		<p><a href="{SITE_URL}/mobile" data-transition="pop">{SITE_NAME}</a>		
		<a href="http://jquerymobile.com/test/" target="_blank" >jQuery Docs</a></p>			
	</div><!-- /header -->	
	<div data-role="content">	
					<h1>{PAGE_CONTENT_TITLE}</h1>
					{MAIN_CONTENT}
	</div><!-- /content -->
	<div data-role="footer" data-theme="c">
		DotKernel Copyright (c) 2009-2010
	</div><!-- /footer -->

</div>
</body>
</html>