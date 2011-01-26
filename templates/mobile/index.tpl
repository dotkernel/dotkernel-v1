<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.1//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{PAGE_TITLE}</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" >
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta name="keywords" content="{PAGE_KEYWORDS}" >
	<meta name="description" content="{PAGE_DESCRIPTION}" >
	<link rel="stylesheet" href="{SITE_URL}/externals/jquery/jquery.mobile-1.0a2.min.css" />
	<script src="{SITE_URL}/externals/jquery/jquery-1.4.4.min.js"></script>
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
	<script src="{SITE_URL}/externals/jquery/jquery.mobile-1.0a2.min.js"></script>
	<script type="text/javascript" src="{TEMPLATES_URL}/js/mobile/main.js"></script>
</head>
<body>
<div data-role="page" data-theme="e"> 
	<div data-role="header" data-theme="b" data-nobackbtn="true"> 
		<p><a href="{SITE_URL}" rel="external" >Web {SITE_NAME}</a></p>			
	</div><!-- /header -->	
	<div data-role="content">	
					<h1>{PAGE_CONTENT_TITLE}</h1>
					{MAIN_CONTENT}
	</div><!-- /content -->
	<div data-role="footer" data-theme="c">
		&copy; 2009 - 2011 DotBoost Technologies Inc.
	</div><!-- /footer -->

</div>
</body>
</html>