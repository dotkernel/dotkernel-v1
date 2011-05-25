<?php 
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotKernel
 * @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */
 
 /**
 * Check DotKernel Requirements for Installation 
 * @author     DotKernel Team <team@dotkernel.com>
 */
$test = true;
//kill the SAFE MODE 
if(ini_get('safe_mode'))
{
	$checkServer['safe_mode'] = array('name'   => 'Safe Mode <b>ON</b>', 
																		'status' => 'failed', 
																		'value'  => 'This feature has been <b>DEPRECATED</b> as of PHP 5.3.0. <br>Relying on
																		             this feature is highly discouraged.');
	$test = false;	
}
// get HostName 
if(version_compare(PHP_VERSION, '5.3.0', '>='))
{
	$hostName = gethostname();
}
else
{
	$hostName = php_uname('n');
}
$checkServer['host'] = array('name'   => 'Host Name', 
														 'status' => 'pass', 
						                 'value'  => $hostName);
// get APACHE VERSION
if(function_exists('apache_get_version'))
{
	$apacheVersion = apache_get_version();
}
else
{
	$apacheTmp = explode(" ", $_SERVER["SERVER_SOFTWARE"], 3);
	$apacheVersion = $apacheTmp[0] . " " . $apacheTmp[1];
}
$checkServer['apache'] = array('name'   => 'Apache Version', 
							                 'status' => 'pass', 
							                 'value'  => $apacheVersion);
// check PHP VERSION
if(version_compare(PHP_VERSION, '5.2.10', '>='))
{
	$checkServer['php'] = array('name'   => 'PHP Version', 
								              'status' => 'pass', 
								              'value'  => PHP_VERSION);
}
else
{
	$checkServer['php'] = array('name'   => 'PHP Version', 
								              'status' => 'failed', 
								              'value'  => 'DotKernel requires <a href="http://php.net/downloads.php">PHP</a> 5.2.10
															             or newer, your version is ' . PHP_VERSION . '.');
	$test = false;
}
// check MySQL Client version
if(function_exists('mysqli_get_client_version') && function_exists('mysqli_get_client_info') )
{
	$mysqlVersion =  mysqli_get_client_version(); #for version 4.1.6 return 40106;
	//create mysql version string to check it
	$mainVersion = (int)($mysqlVersion/10000);
	$a = $mysqlVersion - ($mainVersion*10000);
	$minorVersion = (int)($a/100);
	$subVersion = $a - ($minorVersion*100);
	$mysqlVersion = $mainVersion . '.' . $minorVersion . '.' . $subVersion;
	$mysqlClientVersion = current(explode(' - ', mysqli_get_client_info()));
	if(version_compare($mysqlVersion, '5.0', '>='))
	{
		$checkServer['mysql'] = array('name'   => 'MySQL Client Version', 
									                'status' => 'pass', 
									                'value'  => $mysqlClientVersion);
	}
	else
	{
		$checkServer['mysql'] = array('name'   => 'MySQL Client Version', 
									                'status' => 'failed', 
									                'value'  => 'DotKernel requires <a href="http://dev.mysql.com/downloads/">MySQL</a>
																							 5.0 or newer, your version is ' . $mysqlClientVersion . '.');
		$test = false;
	}
}
else
{
	$checkServer['mysql'] = array('name'   => 'MySQL Client Version', 
								                'status' => 'failed', 
								                'value'  => 'DotKernel requires that your PHP enviroment have <b>MySQLi</b>
																             extension enabled.');
	$test = false;
}
// check MySQL Server version
if( $checkServer['mysql']['status'] != 'failed')
{
 	$mysqlServerVersion = array();
 // check shell_exec only if is not in safe mode 
	if(!array_key_exists('safe_mode' , $checkServer))
	{
			preg_match('/[0-9]\.[0-9]+\.[0-9]+/', shell_exec('mysql -V'), $mysqlServerVersion);		
	}
 // return an empty array ?
	if(!count($mysqlServerVersion)) $mysqlServerVersion[0] = 'N/A';
	$checkServer['mysql_server_version'] = array('name'   => 'MySQL Server Version', 
								                               'status' => 'pass', 
								                               'value'  => $mysqlServerVersion[0]);
}
else
{
		$checkServer['mysql_server_version'] = array('name'   => 'MySQL Server Version', 
								                                 'status' => 'failed', 
								                                 'value'  => 'Unable to test <b>MySQL Server</b> version.');
	$test = false;
}
// check Zend Framework version
$zendExists = @include_once 'Zend/Loader/Autoloader.php';
if($zendExists)
{
	$zend_loader = Zend_Loader_Autoloader::getInstance();
	if(version_compare(Zend_Version::VERSION, '1.11.0', '>='))
	{
		$checkServer['zend'] = array('name'   => 'Zend Framework Version', 
									 							 'status' => 'pass', 
									               'value'  => Zend_Version::VERSION);
	}
	else
	{
		$checkServer['zend'] = array('name'   => 'Zend Framework Version', 
						                     'status' => 'failed', 
									               'value'  => 'DotKernel requires <a href="http://framework.zend.com/download">Zend 
																 			        Framework</a> 1.11.0 or newer, your version is 
																							' . Zend_Version::VERSION . '.');
		$test = false;
	}
}
else
{
	$checkServer['zend'] = array('name'   => 'Zend Framework Version', 
								               'status' => 'failed', 
							                 'value'  => 'DotKernel requires that <a href="http://framework.zend.com/download">Zend 
															              Framework</a> be installed on your server. </br>Check this article
															              <a href="http://www.dotkernel.com/zend-framework/zend-framework-pear-plesk-server/">
																						Zend Framework as PEAR accessible repository on Plesk server</a> for more
																						details on how to install it.');
	$test = false;
}

// check apache module rewrite
if(function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()))
{
	$check['apache_mod_rewrite'] = array('name'   => 'Apache <i>mod_rewrite</i> module', 
										 	                 'status' => 'pass', 
										                   'value'  => 'OK');
}
else
{
	ob_start();
	phpinfo(INFO_MODULES);
	$contents = ob_get_contents();
	ob_end_clean();
	$apacheModule = (strpos($contents, 'mod_rewrite') !== false);
	if($apacheModule)
	{
		$check['apache_mod_rewrite'] = array('name'   => 'Apache <i>mod_rewrite</i> module', 
										                     'status' => 'pass', 
										                     'value'  => 'OK');	
	}
	else
	{
		// if we don't have mod_php, only cgi-fastcgi
		$cgi = (!strpos($contents, 'mod_php') !== false);
		if($cgi)
		{
			 $check['apache_mod_rewrite'] = array('name'   => 'Apache <i>mod_rewrite</i> module', 
											                   'status' => 'pass', 
											                   'value'  => 'You are running Apache with cgi-fastcgi, <br> presence of 
																				 mod_rewrite cannot be determined.');
		}
		else
		{
			$check['apache_mod_rewrite'] = array('name'   => 'Apache <i>mod_rewrite</i> module', 
											                   'status' => 'failed', 
											                   'value'  => 'DotKernel requires Apache mod_rewrite for htaccess route.');
			$test = false;
		}

	}
}
// check ctype	
if(extension_loaded('ctype'))
{
	$check['php_ctype'] = array('name'   => 'PHP <i>Ctype</i> extension', 
															'status' => 'pass', 
															'value'  => 'OK');
}
else
{
	$check['php_ctype'] = array('name'   => 'PHP <i>Ctype</i> extension', 
														  'status' => 'failed', 
															'value'  => 'DotKernel requires <a href="http://www.php.net/manual/en/book.ctype.php">
															             Ctype</a>, used by Zend Framework.');
	$test = false;
}
// check PDO MySQL
if(extension_loaded('pdo_mysql'))
{
	$check['php_pdo_mysql'] = array('name'   => 'PHP <i>PDO_MySQL</i> extension', 
																	'status' => 'pass', 
																	'value'  => 'OK');
}
else
{
	$check['php_pdo_mysql'] = array('name'   => 'PHP <i>PDO_MySQL</i> extension', 
																	'status' => 'failed', 
																	'value'  => 'DotKernel requires 
																	             <a href="http://www.php.net/manual/en/ref.pdo-mysql.php">PDO MySQL</a> 
																							 for database connection. ');
	$test = false;
}
// check session
if(extension_loaded('session'))
{
	$check['php_session'] = array('name'   => 'PHP <i>session</i> extension', 
															  'status' => 'pass', 
															  'value'  => 'OK');
}
else
{
	$check['php_session'] = array('name'   => 'PHP <i>session</i> extension', 
															  'status' => 'failed', 
															  'value'  => 'DotKernel requires <a href="http://www.php.net/manual/en/book.session.php">
																             session</a>, used by Zend Framework. ');
	$test = false;
}
// check SPL
if(function_exists('spl_autoload_register'))
{
	$check['php_spl'] = array('name'   => 'PHP <i>SPL</i> extension', 
													  'status' => 'pass', 
													  'value'  => 'OK');
}
else
{
	$check['php_spl'] = array('name'   => 'PHP <i>SPL</i> extension', 
													  'status' => 'failed', 
													  'value'  => 'DotKernel requires <a href="http://www.php.net/manual/en/book.spl.php">SPL</a>,
														             used by Zend Framework. ');
	$test = false;
}

// check APC
if((function_exists('apc_cache_info') && (@apc_cache_info() !== FALSE)))
{
	$checkOptional['php_apc'] = array('name'   => 'PHP <i>APC</i> extension', 
																	   'status' => 'pass', 
																	   'value'  => 'OK');
}
else
{
	$checkOptional['php_apc'] = array('name'   => 'PHP <i>APC</i> extension', 
																	   'status' => 'failed', 
																	   'value'  => 'DotKernel recommend the use of APC extension for opcode caching. ');
}

// check cURL
if(extension_loaded('curl'))
{
	$checkOptional['php_curl'] = array('name'   => 'PHP <i>cURL</i> extension', 
																	   'status' => 'pass', 
																	   'value'  => 'OK');
}
else
{
	$checkOptional['php_curl'] = array('name'   => 'PHP <i>cURL</i> extension', 
																	   'status' => 'failed', 
																	   'value'  => 'DotKernel requires 
																		              <a href="http://www.php.net/manual/en/book.curl.php">cURL</a> for the
																									 Dot_Curl class.');
}
// check gd
if(extension_loaded('gd'))
{
	$checkOptional['php_gd'] = array('name'   => 'PHP <i>GD</i> extension', 
																	 'status' => 'pass', 
																	 'value'  => 'OK');
}
else
{
	$checkOptional['php_gd'] = array('name'   => 'PHP <i>GD</i> extension', 
																	 'status' => 'failed', 
																	 'value'  => 'DotKernel requires 
																	             <a href="http://www.php.net/manual/en/book.image.php">GD</a>, 
																							 used by Zend Framework and for image manipulation.');
}
// check mbstring
if(extension_loaded('mbstring'))
{
	$checkOptional['php_mbstring'] = array('name'   => 'PHP <i>mbstring</i> extension', 
																			   'status' => 'pass', 
																			   'value'  => 'OK');
}
else
{
	$checkOptional['php_mbstring'] = array('name'   => 'PHP <i>mbstring</i> extension', 
																			   'status' => 'failed', 
																			   'value'  => 'DotKernel requires 
																				              <a href="http://www.php.net/manual/en/book.mbstring.php">mbstring
																											</a>, used by Zend Framework.');
}
//check GeoIp
if(extension_loaded('geoip'))
{
	$checkOptional['php_geoip'] = array('name'   => 'PHP <i>GeoIP</i> extension', 
																			'status' => 'pass', 
																			'value'  => 'OK');
}
else
{
	$checkOptional['php_geoip'] = array('name'   => 'PHP <i>GeoIP</i> extension', 
																			'status' => 'failed', 
																			'value'  => 'DotKernel requires 
																			            <a href="http://www.php.net/manual/en/book.geoip.php">GeoIP</a> used
																									 by the Dot_GeoIP class for faster compilation.');
}

function parseHtmlRows($data)
{
	echo "<tr>";
	foreach($data as $ky => $val)
	{
		?>
				<td class="result">
					<strong><?php echo $val['name'];?></strong>
					<br/><br/>
					<span class="<?php echo $val['status']; ?>"><?php echo $val['value']; ?></span>
				</td>
		<?php
	}
	echo "</tr>";
}
?>

<!-- Start to parse the installation requirements HTML format-->
<!doctype html> 
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<meta http-equiv="X-UA-Compatible" content="IE=9">
<!-- Start to parse the installation requirements HTML format-->
	<title>DotKernel System Requirements Check</title>
<style type="text/css">
	html, body{
		padding:0;
		margin:0;
		color:#404D79;
		background-color:#fff;
	}
	legend{
		font-size:25px;
		padding: 2px 5px 2px 0;
	}
	h1{
		margin-top:40px;
		margin-bottom:0px;
	}
	.intro{
		margin-top:5px;
		font-size:17px;
	}
	fieldset{
		border-color:#404D79;
		border-bottom:none;
		border-left:none;
		border-right:none;
		padding:0;
		margin-bottom:50px;
	}
	#header{
		width:100%;
		background-color:#161B2D;
		background-image:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAAZCAIAAABLixI0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA55JREFUeNpUlUmSHDcMRUkCzKFbLdsR3vgIvv+5JC2kroED6P+BLDvctehMkpgePpj6519/2xwrmchuNs1GWpaLqm5zNFtTdU8pj/5IaeGn28HXgde0be+jP806VlLOupbRPqWZ2nZ82OxprVwEK3CNXXiUuu/nb6PfRTfsjtFglDMODLhea0d40UMR6vW34By2OFcQyN3BwHfgwXxrYikzidjI1/paZZkWUezgRURRiMccZdVaT60bci5FYdnbzWZjIvUoUnE+M5yyxtkYDr5Ed8lMB4Xcf30jrFwijW3/mLOBHeq0+QljLyKXUmWvbh9Z8x/gKANaZ159QwpIF+5wHpb323fGkB3WLBnrbjrHA+zgtO7vdMR6kXpRz24ioOW+H7+Ldd8uvm54nNZVjuPtj9FvIhtW2VOmspz9mdJJ9nXXQAiIjhitY46lSLCPPQ/XbQyPny/w3oG1ZrAnPNSVlO0QrRPs+xPboOvsqTg8w6o/fyEL4KvbGezhDeCQI0QTAnL2rGfigeyZPdsK9nX/gnMIBi84CiLuAvS0bJF1+U9Qc2h7/IS4Q58IOGfPbCXrgmvWKxXkg70XZb3d53jCvm5vrO5in5WYoXWmttAX8WfamDkTeBi5nuf5tbdP1QPgfJ5ia8JdUFMwodMCQV32/2PvjP0hIRH0bwZw7PrYZRF4sensM9gDbZaL/Qjdgzc0/wb8zp69RlJUrItApAajYG8T9WZnL1sSJoSu3duPdNVP8SBtNC7Y+zAHaeal8fxa8fNTG5o9kfAaDX6he4tZw+/++Y35vnRPSbsRx5a6TxiyVxsjAKfPlgsV7FFy9HRdw5ZtjVpOXEeQmLLqEexdOgNyW+uAB9TuugdLXjgFCU6/m6hh3h+YSiugiayhyWgL5V3Mq0NIzgOvUvYOddXCLGB/iZhjARfU/mGc7YqjYO9dHjW/Fdmu0cs6BrTWAAwuAVGpS/ZxH+0epcd9JPXIfuckczH7wIf64C4eQqUxKhqDxku1iMix0hOMUS/OPKD75Lov6t2cyeccM/ti/yVmjvEzmwvdr7gl6n6WIc6++OTx8qMaVPbjA9MGwKyT7HPMc4wRplH3d412whMAofLJeyLYV4bhHc3d1m5k35bnWMzrChP/dDE07omLPb4xvT18hiARllyp1YEDONmfN/9iIRFx9jFeOvvFHvcK9CVZCnMpUNb9RZTws/JmwQGc/Jd0NDAG62rm9U1K/wgwAMuu+WwaAQZSAAAAAElFTkSuQmCC');
		height:70px;
	}
	#header div{
		color:#fff;
		line-height:65px;
		font-size:20px;
		padding-top:8px;
		margin: 0 auto;
		width:1200px;
		height:70px;
	}
	#header div, h1, legend{
		font-family: Arial, sans-serif;
	}
	#wrap{
		font-family:"Lucida Sans Unicode","Lucida Grande",Garuda,sans-serif;
		margin: 0px auto;
		width: 1200px;
		padding: 5px 35px 35px 35px;
	}
	table{
		border-spacing:5px;
	}
	td.result{
		font-family:"Lucida Sans Unicode","Lucida Grande",Garuda,sans-serif;
		font-size:13px;
		padding: 30px 10px;
		text-align:center;
		background-color:#F1F2F6;
	}
	td.result strong{
		font-size:16px;
		font-family: Arial, sans-serif;
		font-weight: bold;
	}
	.pass {
		color: #12A30E;
		font-weight: bold;
	}
	.failed{
		color: #B8250C;
		font-weight: bold;
	}
	.testpass{
		margin: 30px 0px 20px 0px;
		font-size: 15px;
	}
	.testpass p{
		margin: 0px 0px 10px 0px;
		padding: 0px;
		font-size: 24px;
		color: #1B7A0A;
		font-weight: bold;
		line-height: 29px !important;
		font-family: Arial, sans-serif;
	}		
	.testfailed{
		margin: 30px 0px 20px 0px;
		font-size: 15px;
	}
	a{
		color: #137dd7;
	}
	.testfailed p{
		margin: 0px 0px 10px 0px;
		padding: 0px;
		font-size: 24px;
		color: #FF0000;
		font-weight: bold;
		line-height: 29px !important;
		font-family: Arial, sans-serif;
	}
</style>
</head>

<body>
<div id="header"><div><img alt="DotKernel" src="data:image/png;base64,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" /></div></div>
<div id="wrap">
	<h1>DotKernel  System Requirements Check</h1>
	<p class="intro">
		We have run the below tests to determine if DotKernel will work in your environment.<br/>
		If any of the tests have failed, consult the <a href="http://www.dotkernel.com/docs/system-requirements/">system requirements</a> page for more information. 
	</p>
	
<?php
if($test)
{
	?>
	<div class="testpass">
		<p>Your enviroment passed all the requirements needed to run DotKernel.</p>
		You can now delete or rename the <i>dk.php</i> file.</div>
	<?php
/*} 
else
{*/
	?>
	<div class="testfailed">
		<p>The test has failed.</p>
		Check <a href="http://www.dotkernel.com/docs/system-requirements/">system requirements</a> page for more information.
	</div>
	<?php
}
?>
	
	<fieldset>
		<legend>Enviroment Test</legend>
		<table width="100%" cellpadding="5" cellspacing="1">
		<tbody>
		<?php
			parseHtmlRows($checkServer);
		?>
		</tbody>
		</table>
	</fieldset>
	<fieldset>
		<legend>Extensions Test</legend>
		<table width="100%" cellpadding="5" cellspacing="1">
		<tbody>
		<?php
			parseHtmlRows($check);
		?>
		</tbody>
		</table>
	</fieldset>
	<fieldset>
		<legend>Optional Extensions Test</legend>

		<p class="note">The following extensions are optional for DotKernel, but if used can provide access to more classes. </p>
		<table width="100%" cellpadding="5" cellspacing="1">
		<tbody>
		<?php
			parseHtmlRows($checkOptional);
		?>
		</tbody>
		</table>
	</fieldset>
</div>
</body>
</html>