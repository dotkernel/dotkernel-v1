<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotKernel
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * Check DotKernel Requirements for Installation
 * 
 * @author DotKernel Team <team@dotkernel.com>
 */
$test = true;
// prevent date warnings
date_default_timezone_set('America/New_York');

function checkAllPaths($includePathLocation)
{
	return file_exists($includePathLocation . '/Zend/Loader/Autoloader.php');
}

function parseHtmlRows($data)
{
	foreach($data as $ky => $val)
	{
		echo <<<EOD
			<tr>
				<td width="20%">$val[name]</td>
				<td class="$val[status]" width="35%">$val[value]</td>
			</tr>
EOD;
	}
}

function testSafeMode()
{
	// //kill the SAFE MODE
	if(ini_get('safe_mode'))
	{
		$checkServer['safe_mode'] = array(
						'name' => 'Safe Mode <b>ON</b>',
						'status' => 'failed',
						'value' => 'This feature has been <b>DEPRECATED</b> as of PHP 5.3.0.');
		return false;
	}
	return true;
}

function checkPhpServer()
{
	// get HostName
	$hostName = php_uname('n');
	$checkServer['host'] = array('name' => 'Host Name', 'status' => 'pass', 'value' => $hostName);
	
	// get Web Server , nicely formatted
	$webServerTmp = explode(" ", $_SERVER["SERVER_SOFTWARE"], 3);
	$webServer = $webServerTmp[0];
	$checkServer['webServer'] = array('name' => 'Web Server', 'status' => 'pass', 'value' => $webServer);
	
	// check PHP VERSION
	if(version_compare(PHP_VERSION, '5.4.0', '>='))
	{
		$checkServer['php'] = array('name' => 'PHP Version', 'status' => 'pass', 'value' => PHP_VERSION);
	}
	else
	{
		$checkServer['php'] = array(
						'name' => 'PHP Version',
						'status' => 'failed',
						'value' => 'Your version of PHP:  <b>' . PHP_VERSION . '</b> is at End-Of-Life. Please upgrade.');
	}
	// check composer here !!! 
	
	return $checkServer;
}

function checkApacheServer()
{
	// check apache module rewrite
	if(function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()))
	{
		$checkServer['apache_mod_rewrite'] = array('name' => 'Apache mod_rewrite', 'status' => 'pass', 'value' => 'OK');
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
			$checkServer['apache_mod_rewrite'] = array('name' => 'Apache mod_rewrite', 'status' => 'pass', 'value' => 'OK');
		}
		else
		{
			// if we don't have mod_rewrite, only some sort of CGI bridge
			$checkServer['apache_mod_rewrite'] = array(
							'name' => 'Url Rewrite',
							'status' => 'confused',
							'value' => strtoupper(PHP_SAPI));
		}
	}
	return $checkServer;
}

function checkMySql(&$test)
{

	// check MySQL Client version
	if(function_exists('mysqli_get_client_version') && function_exists('mysqli_get_client_info'))
	{
		$mysqlVersion = mysqli_get_client_version(); // or version 4.1.6 return 40106;
		// create mysql version string to check it
		$mainVersion = (int) ($mysqlVersion / 10000);
		$a = $mysqlVersion - ($mainVersion * 10000);
		$minorVersion = (int) ($a / 100);
		$subVersion = $a - ($minorVersion * 100);
		$mysqlVersion = $mainVersion . '.' . $minorVersion . '.' . $subVersion;
		$mysqlClientVersion = current(explode(' - ', mysqli_get_client_info()));
		if(version_compare($mysqlVersion, '5.0', '>='))
		{
			$checkServer['mysql'] = array('name' => 'MySQL Client Version', 'status' => 'pass',
							'value' => $mysqlClientVersion);
		}
		else
		{
			$checkServer['mysql'] = array('name' => 'MySQL Client Version', 'status' => 'failed',
							'value' => 'DotKernel requires <a href="http://dev.mysql.com/downloads/">MySQL</a> 5.0 or newer,
																							your version is ' . $mysqlClientVersion . '.');
			$test = false;
		}
	}
	else
	{
		$checkServer['mysql'] = array('name' => 'MySQL Client Version', 'status' => 'failed',
						'value' => 'DotKernel requires that your PHP enviroment have <b>MySQLi</b> extension enabled.');
		$test = false;
	}
	// check MySQL Server version
	if($checkServer['mysql']['status'] != 'failed')
	{
		$mysqlServerVersion = array();
		// check shell_exec only if is not in safe mode
		if(! array_key_exists('safe_mode', $checkServer))
		{
			preg_match('/[0-9]\.[0-9]+\.[0-9]+/', shell_exec('mysql -V'), $mysqlServerVersion);
		}
		// return an empty array ?
		if(! count($mysqlServerVersion)) $mysqlServerVersion[0] = 'N/A';
		$checkServer['mysql_server_version'] = array('name' => 'MySQL Server Version', 'status' => 'pass',
						'value' => $mysqlServerVersion[0]);
	}
	else
	{
		$checkServer['mysql_server_version'] = array('name' => 'MySQL Server Version', 'status' => 'failed',
						'value' => 'Unable to test <b>MySQL Server</b> version.');
		$test = false;
	}
	return $checkServer;
}

function checkZendFramework(&$test)
{
	// check if Zend Framework is installed, if is in include_path and its version
	// get open_basedir and include path locations. If open basedir is not set, then
	$openBasedirArray = (ini_get('open_basedir')) ? explode(PATH_SEPARATOR, ini_get('open_basedir')) : null;
	$includePathArray = explode(PATH_SEPARATOR, get_include_path());
	
	// if open_basedir is not set, we do not need to calculate the intersection
	$openBasedirInclude = $includePathArray;
	if(! is_null($openBasedirArray))
	{
		$openBasedirInclude = array_intersect($openBasedirArray, $includePathArray);
	}
	
	$zendExist = array_map('checkAllPaths', $openBasedirInclude);
	if(in_array(1, $zendExist))
	{
		include_once 'Zend/Loader/Autoloader.php';
		$zendLoader = Zend_Loader_Autoloader::getInstance();
		if(version_compare(Zend_Version::VERSION, '1.11.0', '>='))
		{
			$checkServer['zend'] = array('name' => 'Zend Framework', 'status' => 'pass', 'value' => Zend_Version::VERSION);
		}
		else
		{
			$checkServer['zend'] = array('name' => 'Zend Framework', 'status' => 'failed',
							'value' => '<a href="http://framework.zend.com/downloads/latest" target="_blank">Zend Framework</a>
																							1.11.0 or newer is required, your version is only ' .
							Zend_Version::VERSION . '.');
			$test = false;
		}
	}
	else
	{
		$checkServer['zend'] = array('name' => 'Zend Framework', 'status' => 'failed',
						'value' => '<a href="http://framework.zend.com/downloads/latest" target="_blank">Zend Framework</a> is missing.
																						Check <a href="http://www.dotkernel.com/zend-framework/zend-framework-pear-plesk-server/" target="_blank">
																						this article</a> for more details on how to install it. Also check your <b>include_path</b> and
																						<b>open_basedir</b> directives in your php.ini.');
		// List include path and open basedir folders
		$checkServer['includePath'] = array('name' => 'Include Path', 'status' => 'confused',
						'value' => get_include_path());
	
		$checkServer['openBasedir'] = array('name' => 'Open Basedir', 'status' => 'confused',
						'value' => ini_get('open_basedir'));
		$test = false;
	}
	return $checkServer;
}

function checkMbString()
{
	if(extension_loaded('mbstring'))
	{
		$checkUtf8['php_mbstring'] = array('name' => 'mbstring', 'status' => 'pass', 'value' => 'OK');
	}
	else
	{
		$checkUtf8['php_mbstring'] = array(
						'name' => 'mbstring', 'status' => 'failed',
						'value' => 'DotKernel requires <a href="http://www.php.net/manual/en/book.mbstring.php">mbstring
		</a> extension, used by Zend Framework.');
	}
	return $checkUtf8;
}

function checkCharacterEncoding()
{
	// check default charset UTF-8 related
	$defaultCharset = ini_get('default_charset');
	if(stristr($defaultCharset, 'utf-8'))
	{
		$checkUtf8['php_charset'] = array('name' => 'Default charset', 'status' => 'pass', 'value' => 'UTF-8');
	}
	else
	{
		$currentCharset = $defaultCharset ? $defaultCharset : 'PHP default charset is not set.';
		$checkUtf8['php_charset'] = array('name' => 'Default charset', 'status' => 'hmmm', 'value' => $currentCharset);
	}
	$checkUtf8 = array_merge($checkUtf8, checkMbString());
	return $checkUtf8;
}

// Required Extensions

function checkPdoMysqlExtension(&$test)
{
	// check PDO MySQL
	if(extension_loaded('pdo_mysql'))
	{
		$check['php_pdo_mysql'] = array('name' => 'PDO MySQL', 'status' => 'pass', 'value' => 'OK');
	}
	else
	{
		$check['php_pdo_mysql'] = array('name' => 'PDO MySQL', 'status' => 'failed',
						'value' => 'By default, DotKernel use  <a href="http://www.php.net/manual/en/ref.pdo-mysql.php">PDO MySQL</a>
																							 driver. ');
		$test = false;
	}
	return $check;
}

function checkMcryptExtension(&$test)
{
	if(extension_loaded('mcrypt'))
	{
		$check['php_mcrypt'] = array('name' => 'Mcrypt', 'status' => 'pass', 'value' => 'OK');
	}
	else
	{
		$check['php_mcrypt'] = array('name' => 'Mcrypt', 'status' => 'failed',
						'value' => 'DotKernel requires <a href="http://php.net/manual/en/book.mcrypt.php"> Mcrypt</a>. ');
		$test = false;
	}
	return $check;
}

function checkJsonExtension(&$test)
{
	// check JSON
	if(function_exists('json_decode'))
	{
		$check['php_json'] = array('name' => 'JSON', 'status' => 'pass', 'value' => 'OK');
	}
	else
	{
		$check['php_json'] = array('name' => 'JSON', 'status' => 'failed',
						'value' => 'DotKernel requires <a href="http://www.php.net/manual/en/book.json.php">JSON</a> library. ');
		$test = false;
	}
	return $check;
}

// check call for all required extensions
function checkRequiredExtensions(&$test)
{
	// default extensions for PHP 7
	/*
	 bz2, calendar, ctype, curl, date, exif, fileinfo, filter, ftp, gettext, gmp,
	 hash, iconv, json, libxml, openssl, pcntl, pcre, Phar, readline, Reflection,
	 session, shmop, SimpleXML, sockets, SPL, standard, tokenizer, xml, zip, zlib
	 */
	// check mbstring UTF-8 related
	
	#'', 'mcrypt'
	
	$check = array();
	$check = array_merge($check, checkPdoMysqlExtension($test));
	$check = array_merge($check, checkMcryptExtension($test));
	$check = array_merge($check, checkJsonExtension($test));
	
	return $check;
}

// Optional Extensions
function checkCurl()
{
	// check cURL
	if(extension_loaded('curl'))
	{
		$checkOptional['php_curl'] = array('name' => 'cURL', 'status' => 'pass', 'value' => 'OK');
	}
	else
	{
		$checkOptional['php_curl'] = array('name' => 'cURL', 'status' => 'hmmm',
						'value' => 'DotKernel requires <a href="http://www.php.net/manual/en/book.curl.php">Client URL</a>
																									library.');
	}
	return $checkOptional;
}

function checkGd()
{
	// check gd
	if(extension_loaded('gd'))
	{
		$checkOptional['php_gd'] = array('name' => 'GD Lib', 'status' => 'pass', 'value' => 'OK');
	}
	else
	{
		$checkOptional['php_gd'] = array('name' => 'GD Lib', 'status' => 'hmmm',
						'value' => 'DotKernel requires <a href="http://www.php.net/manual/en/book.image.php">GD</a> library,
					for image manipulation.');
	}
	return $checkOptional;
}

function checkOpcache()
{
	// check Opcache
	$opcacheStatus = false;
	if(function_exists('opcache_get_status'))
	{
		$opcacheStatus = opcache_get_status();
	}
	
	
	if(is_array($opcacheStatus) && $opcacheStatus['opcache_enabled'] == true )
	{
		$checkOptional['opcache'] = array('name' => 'opcache', 'status' => 'pass', 'value' => 'installed and enabled');
	}
	else
	{
		$checkOptional['opcache'] = array('name' => 'opcache',
						'status' => 'hmmm',
						'value' => 'Opcache does not work. Your PHP code will run faster if the <a href="http://php.net/manual/en/book.opcache.php">opcache</a>
 is enabled');
	}//*/
	return $checkOptional;
}


/*
APC TEST
// check APC . First for APCu, then if is not present, check for old APC
$apcu= phpversion('apcu');
if($apcu){
	$apcVersion = $apcu;
	$apcExtensionName = 'APCu';
}
else{
	$apcVersion = phpversion('apc');
	$apcExtensionName = 'APC';
}

if($apcVersion)	$checkOptional['php_apc'] = array('name' => $apcExtensionName , 'status' => 'pass', 'value' => $apcVersion);
else $checkOptional['php_apc'] = array('name' => $apcExtensionName, 'status' => 'hmmm', 'value' => 'DotKernel recommend the use of APC or APCu extension for local user value caching. ');
 */

function checApcu(){}

function checkGeoIpLegacy()
{
	// check GeoIp
	$geoIpVersion = phpversion('geoip');
	if($geoIpVersion)
	{
		$checkOptional['php_geoip'] = array('name' => 'GeoIP', 'status' => 'pass', 'value' => $geoIpVersion);
	}
	else
	{
		$checkOptional['php_geoip'] = array('name' => 'GeoIP',
						'status' => 'hmmm',
						'value' => 'DotKernel recommends <a href="http://www.php.net/manual/en/book.geoip.php">GeoIP</a>
		  extension used by the Dot_GeoIP class for faster detection.');
	}
	return $checkOptional;
}

function checkGeoIp2()
{
	#mmdblookup | maxminddb
	// check GeoIp
	$geoIp2Version = phpversion('maxminddb');
	if(extension_loaded('maxminddb'))
	{
		$checkOptional['maxminddb'] = array('name' => 'GeoIP2', 'status' => 'pass', 'value' => $geoIp2Version);
	}
	else
	{
		$checkOptional['maxminddb'] = array('name' => 'GeoIP2',
			'status' => 'hmmm',
			'value' => 'DotKernel recommends <a href="https://dev.maxmind.com/maxmind-db/">MaxMind DB</a>
		  extension used by the Dot_GeoIP class for faster detection .');
	}
	return $checkOptional;
}

// check call for all optional extensions
function checkOptionalExtensions()
{
	$checkOptional = array();
	$checkOptional = array_merge($checkOptional,checkCurl());
	$checkOptional = array_merge($checkOptional,checkGd());
	$checkOptional = array_merge($checkOptional,checkOpcache());
	$checkOptional = array_merge($checkOptional,checkGeoIpLegacy());
	$checkOptional = array_merge($checkOptional,checkGeoIp2());
	#$checkOptional = array_merge($checkOptional,checApcu());
	return $checkOptional;
}


// running the tests 
if( testSafeMode()  == false )
{
	$test = false;
}
$checkServer = array(); 
$checkServer = array_merge($checkServer,checkPHPServer()) ;
$checkServer = array_merge($checkServer,checkMySql($test)) ;
$checkServer = array_merge($checkServer,checkZendFramework($test)) ;
$checkServer = array_merge($checkServer,checkApacheServer()) ;
$checkUtf8 = checkCharacterEncoding();

$check = checkRequiredExtensions($test);
$checkOptional = checkOptionalExtensions(); 


// Start to parse the installation requirements HTML format
?>
<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<title>DotKernel System Requirements Check</title>
<style type="text/css">
body {
	text-align: center;
	background-color: #ffffff;
	font-family: Lucida sans unicode, sans-serif;
	font-size: 13px;
	line-height: 20px;
	padding: 0px;
	margin: 0px;
}

h1 {
	font-size: 32px;
	font-weight: normal;
	color: #272727;
	margin: 20px 0 0 0;
	line-height: 40px;
}

a {
	color: #0F83C8;
	text-decoration: none;
}

a:hover {
	color: #EE6C25;
	text-decoration: underline;
}

p {
	padding: 0px;
	margin: 10px 0px;
}

#header {
	width: 100%;
	background-color: #161B2D;
	background-image:
		url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAAZCAIAAABLixI0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA55JREFUeNpUlUmSHDcMRUkCzKFbLdsR3vgIvv+5JC2kroED6P+BLDvctehMkpgePpj6519/2xwrmchuNs1GWpaLqm5zNFtTdU8pj/5IaeGn28HXgde0be+jP806VlLOupbRPqWZ2nZ82OxprVwEK3CNXXiUuu/nb6PfRTfsjtFglDMODLhea0d40UMR6vW34By2OFcQyN3BwHfgwXxrYikzidjI1/paZZkWUezgRURRiMccZdVaT60bci5FYdnbzWZjIvUoUnE+M5yyxtkYDr5Ed8lMB4Xcf30jrFwijW3/mLOBHeq0+QljLyKXUmWvbh9Z8x/gKANaZ159QwpIF+5wHpb323fGkB3WLBnrbjrHA+zgtO7vdMR6kXpRz24ioOW+H7+Ldd8uvm54nNZVjuPtj9FvIhtW2VOmspz9mdJJ9nXXQAiIjhitY46lSLCPPQ/XbQyPny/w3oG1ZrAnPNSVlO0QrRPs+xPboOvsqTg8w6o/fyEL4KvbGezhDeCQI0QTAnL2rGfigeyZPdsK9nX/gnMIBi84CiLuAvS0bJF1+U9Qc2h7/IS4Q58IOGfPbCXrgmvWKxXkg70XZb3d53jCvm5vrO5in5WYoXWmttAX8WfamDkTeBi5nuf5tbdP1QPgfJ5ia8JdUFMwodMCQV32/2PvjP0hIRH0bwZw7PrYZRF4sensM9gDbZaL/Qjdgzc0/wb8zp69RlJUrItApAajYG8T9WZnL1sSJoSu3duPdNVP8SBtNC7Y+zAHaeal8fxa8fNTG5o9kfAaDX6he4tZw+/++Y35vnRPSbsRx5a6TxiyVxsjAKfPlgsV7FFy9HRdw5ZtjVpOXEeQmLLqEexdOgNyW+uAB9TuugdLXjgFCU6/m6hh3h+YSiugiayhyWgL5V3Mq0NIzgOvUvYOddXCLGB/iZhjARfU/mGc7YqjYO9dHjW/Fdmu0cs6BrTWAAwuAVGpS/ZxH+0epcd9JPXIfuckczH7wIf64C4eQqUxKhqDxku1iMix0hOMUS/OPKD75Lov6t2cyeccM/ti/yVmjvEzmwvdr7gl6n6WIc6++OTx8qMaVPbjA9MGwKyT7HPMc4wRplH3d412whMAofLJeyLYV4bhHc3d1m5k35bnWMzrChP/dDE07omLPb4xvT18hiARllyp1YEDONmfN/9iIRFx9jFeOvvFHvcK9CVZCnMpUNb9RZTws/JmwQGc/Jd0NDAG62rm9U1K/wgwAMuu+WwaAQZSAAAAAElFTkSuQmCC');
	height: 70px;
}

#header div {
	color: #fff;
	line-height: 65px;
	font-size: 20px;
	padding-top: 8px;
	margin: 0 auto;
	width: 700px;
	height: 70px;
	text-align: left;
}

.wrap {
	margin: 0px auto;
	padding: 2px 0 0 0;
	width: 800px;
	background: #FCFCFC;
}

.intro {
	margin: 0px;
	padding: 0px 0px;
	font-size: 14px;
	line-height: 20px;
	color: #999999;
}

table {
	color: #4A4A4A;
	width: 100%;
	border-spacing: 0;
	border-collapse: collapse;
}

table td {
	text-align: left;
	padding: 5px;
}

li {
	list-style: circle inside;
}

.req {
	padding: 10px 0;
	margin: 0 0 40px 0;
	border-top: 1px solid #D8D8D8;
	border-bottom: 1px solid #D8D8D8;
}

.req h2 {
	margin: 0px;
	text-align: center;
	color: #C0C0C0;
	font-size: 28px;
	font-weight: normal;
	line-height: 36px;
}

.status_ok {
	padding: 30px 0;
}

.status_ok p {
	margin: 0px 0 0px 0;
	color: #438E2D;
	font-size: 21px;
}

.status_ok span {
	color: #999999;
}

.status_not_ok {
	padding: 30px 0;
}

.status_not_ok p {
	margin: 0px 0 0px 0;
	color: #F12F2F;
	font-size: 21px;
}

.status_not_ok span {
	color: #999999;
}

.pass {
	color: #108D0A;
}

.failed {
	color: #F12F2F;
}

.hmmm {
	color: #F86914;
}

.confused {
	color: #537EC8;
}
</style>
</head>

<body>
	<div id="header">
		<div>
			<img alt="DotKernel"
				src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIIAAAAeCAYAAADgp8bFAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAD7dJREFUeNrsWwmQHNV5/l9f0zOzs3OsdlfaQ3voQNIiwBGykSAQZGNslFjmtp3CzpJyJTbY5YtAxBEHywQbXMTGdrmcYOMDY8Agk9icJgaMLVkgBEhBSNrVhaRdaY/ZOXaOvl7+1/330mrNKoJUtuSqadWn7p5+/d7r93/v+///da/C153VYze0XA/x9EqJ2xJYVQ62AWBWgSHAqLrH+Lt3LK7ZlgvLMMuOZR3SmPHiC4XMQ5OWvFOJcHjvtXgdGBQLNqg21pNOwvBBA0yTAWNwzBbTTPjRxj74/UA7RDXL/W3u3LnQ398P8Xgc6tv//6bw1Jy75fYlq11jVwoAVQlca3HuwQFvD+JY7B1hY+9mFc+ZBZOGsnrEVF4pmfLO3l7Tu87/jx1TFBf1baaIMKt7NUxOAIztRRvb3qy33lICVxWmIJSi4llZkEONYg0ybJ3Un9xX0p42KhJ84BwsJ3MFLFiKZbZiYeuddCwSiYAsy3ULzdAmSRbO4OxBz8iOc+xUZkFwUgssJ6sA0RiMGVp+R1lfJzFmrPorE1JtWK5sLwe15XFFT/0AK21+J/LQ0NBQJ8JMKgJUJykGQCIwdAuO5RkajcfCBvRPBRniaeC425xn94zz2Mb2TgvO/ACSqggSVxrWMrWlNZKCq2zJmWfx6kfxrv1vp2OpVAqbYXULzZQigFEh2a8Q0JhCJWzbUwiH9lMxA57rjagGcRiYcPbsmIzcAaYFK87He1yvwS5jsdl/CdwAZudATnavBCf+M86dzDEC4yNkb0GApqamunVmlAhmGabIYJEyIBGYbXrq4BLCIkLgXtEAknOgOFm1Xs5ZN9pWZaitrQpzeoRcSGlQ0mtBSmAd41jvfowlc6An2s9ONMS/gRWoJlZzDCyKR2lTVRVaWlrq1plRIlRLGBBWEDiDDYTlwVUFyzyaBMJlpDuASwpsPlx8KGfmH+npmIA1nzAwg3CAy7FrQOs83Y0PHcxAGPr46hAeVyDVMuevNVW+omxwKGO1U8CmKiZHIvApZUin05BMJuvWmVkilANuwYdByiDgrRm4ipHAWZqYBfsOj7+5q6zcgperXUtioKcxcDTsU5na9llQUdLtMSSB4xFBGBfJIGm6msykrsezhIShiMQ8yDKHkqFBoaLhuScLXV1d9fWDmY8RSp5rMIgERiBesIkMgiwiLmiZD/mJcXPDsHl9uaIOnP4XKTjjQ5gUWLLGtaavgL64GZwsKkCOSCBRAIr3V7OQSDctRTW58vA4g5Gch1zRgY27m2HroVbQFNvtVHd3N6YzUt06M5s1CCKQKjAafDc4tGkFEfVbR5nuOA2syiRs2JO9Z6gauR9sCRYtQ3I4JnAlchWLrVgDDOMHcy9Ff7QoJfbi3JwAhmlEJhn9yL4DhR9qGnOtzi2OXoeDIntqkMlk3FVFcSgWGL0VLFdXsKOQR0wIjZnmeToRPSLMQGA6BAOIUbomctFekZki7JqTAmAfApkMbdT+DoQZKCPq6EPg7ICddM98hE79hFBZzMvhCAIHBuaJ5ZFAOfFM6D/hEHhh9lTmjFggknBqP5i64ayD2YhddI9ovxuRrPFM4hpKM7xJx/OozJ5ay32KRwDss4pSLMk0k8l4orgIDmf1IB9KsGHH/l/vLsprZbx00Yc6oIkjEQqDi1ly0T9BdCGD/DN4TxHvVTwSiDpEF2yhCiIQrWA20LCsNT2xzDDlTeJyBAkQ1QQHFddN9Pb2ioxB3PUVxFXU6QgRQRhpEPEA4qcBQggCfBnxPiKPv21D3I+4A7Ec8QgZ2KKB5mQkjQz374hrqO4/Q1yJeDIwsKKNzyPwQeEyxPmIn1D/VCrjb6LOXyKuQFyE+B4RRqW2BCFQOmE74k7EY3TfzYhPUf/uRqwLEOtriEuoDz9EnEpj0eba8uj2ZSLBUsQpiP9AYAQPFyN2H0sEx/RkXywQyYq3l8ReEw7cJUK1kIMXd+19bOcE70dbZS/6YAcsWJJCTpZkrs2+jSXO7YQKKoGxm54zuOhAqiDIiC5Cj0VT+aLStW+IbVKxGRVdwOGSJBYo3SXlvr4+sZAkHqhLxBM08/5Ig70YcQEZvB1xK82Ge+h3MfvvQmBn4D2IjyG+Sp34GQ12nDq0GhFDbKIZJoz0SkA5kjQD/e0sxBcRUcTDpBRidraQ0Z6kvTQ1tgAvUdvCUK2kAE8SqQVRlhGZzkCsRLxBbScI/4h4DvE76lcn9auN2kiSIoix+Q3icMAAEo2HSc/RQWrTWNM1PHPQ+TES0/JWkBi9R/AzfPE/V/IVe2upXPx+QmKFjgtmgTpvBMbVw1KmK/E51nDph90xLD6PNVTfUj//fQP36xNrEAZyrQFmNSlLDcd8SEFp0RQOk2PoHkawpx0d0NPT47PHX5r+T8SX6FinwbmFZu53yEAXkCv4OOLxkCLchvgszZyrA7P1dZLLb9Os9nvcQNIP5IaAjLKOSPAE4sf0u99HsVh2+XGW033ZHqQ+Ful8FvVRkOQcIoLftkNE/RfE+4lkvgKagXo5Eewz5ErgOH2wpnGLoGzPmp8IegwOXioXXE1Msyq82tsPrQt1OH/RvZArx2CyzPpiRuNNuqJ7ShJHexQ34GOMe5OBB8TAP8A0VKxXxmKsM9XIXcGJIBFiJe6+z1q+fDlei4X7GFxuEn7xXnroFooHltC1LYjnQ/f6JBIDvSggiU6gXim8bhq4X7imMxHn0swVRvuHGuPYQsozGVADQcgNoXKCUGcjRuhYkDhNbYfl+gmSdFH+WsTXj2NgndTqAPVftP8iuYMTCxZ/cOZDXjyHRpFRpiVZyDT6a8ztxLH4LcZMGI71ghbT4dn9F8MXm2+FzN6X9m5+ZeBT8+d97/LOeUsvSHauSICKap19Cs01SArlv7EMQuy4zrjDGBf/vFXLuZ3tsHDhwlp9DK8z5wPBVYSk3v89HESWyTgZKgsh49eqP7hdETrHwYKtNcqlaxBErkEEIfvrqX2FyrxGscBzobIbET8iJRN1P00zvxYRhMJ9MvT7o4hf1QhiaxPhcMOiuNB/Ed+JlE286FEVjwgyOm5010xnToVV87ZdtWDAWgI3j34bPpnrL7RWN/x87GD05wcGx85b0Pf6TT1Lz3+flFqFIQkavIoE59Jbc80VBclLIExeLlUYF67BRliOBueec5b7oqnGFn6QHvKNnKR7JBAwJilSDs7UDElp/h1kVcIN/RbxQcRaOh+geCO47Se3kwv41d016hslRfNJ1kWZxX01JDtOsYgw6BoKeGtnfh7ZP00k9YPGodDYsZDLPboSRTKfYzg3FSymoeGFVAu/raEaqKqD5xJTZWZUJfVQyYZfx1jxd2Pl+MC3rK9Dm7YLOsc3wMfT9z03sG34pYlDj37tXavOu0ZqRDUdR/Jaw9QvYgJTcfI7MF609w3nZBCfG2hIvvicFbD01MXTGaORfGmMgqSb6fg1MkqCFEJE0H+H+CYNTBvFBmJA/5sCwbe7vUGBqkATSbRwAS+Q8VkgVthPhpYDapUkcvjbIcqGCpTBrCeSiWzk+lDbESLHP5Nrem8gfggrpk2EGqD7/GyoiSaGPxVlGpcxuu7fX1YkVVkmjmU3cEMiqAx0VYIoMiOiyhDFY11jEE0lROxwabaoHjkwmn94xGr97lanfdsfc12gSw5c0nDf5M7NuWv1+O/lvhV//vcQRcMamO1x461uYDxRKVehkHfesAzJ7X4k0QoL3nUJaJoWfkCd9lcHIvxUYAbeRK5ASOi/Im4gI11Og9JHEfUEkWesxoyDQJQd3GIBEvrbOjLIadTO34Yk/wUiBAsY8nGKM1hgjSBGRNhIgeA3Sfp/S3GBHuqXiH1up9RRC12T6Tcx2x6kbEQOrWOcRe3LpI4PBNYg/HL75MyF133Ztk2wxadn6M8Nm0PF4lBCMS0gcgaDiYqEQLui0MQjcrw5qS9Hl7GmXMwWpMnhl7fkl4DtqNBnbYFJWXm2OTmxSm9s63BfWJljtKaAfY22QnYkmx08ULkb7X5Ii6jQ8+5PQ2bu2bVmY5wGbBvlw4OUjt1PJPhDoOyzFDEblCbNoZxZyOqNgbWA4Av1Rhqo9TRTgz63g1zJ/SSxQCqzjWaZGMSnqH++vI9Qm6NEulFSkmDq9zwZ25fnVwOuZJxiigQ9+y8osxHby2R8i1TqAVoYAgqExyh1zFK7o3T8OsUJBqW6Rwjj4XKs984hblkWiKBNlsXnYSJARNl2g0bZjRmY8O2S/6WaA0nkbHNchmzR4Ht2bFtbyBVun6MMwb2n3wiRCGYDqnTh3N6eR1W9KQI5fFYLx1DDVDbeCi9v2vNf23ea71dVZrcsvhi6VnwGJEWffgn86JW4/+0LF0YGlmjQCidQnk/TrhZa8YPAbFRDC1JyjXoYlQlKvTlN8BYjAvrBbjR073Tl/BhBnab9SuD345abip69jJFPfXfgfnqA/zloeIdj2ue+gbRx5jswnDdh4EgFVE1l7Z29tzodnf0DIzJc9+JHkQSKWKV6ppQdesxdTVSS3rjqTVAp5OHgcPU+WeZ2smM5tC/rPx4JwoHiiXzmxMknZ0+ABMer05mGBH4OXwrca5DRKiGEDVk9TgRfChm3fILlILBKWqt9fqLlAkRA4zvC8CKnd1wC2LaA7cIyEbgXpGBYNo8ZxJtjJVDijWqznrijQSot+sNQN3z++csgojHLKJce5BVMH1ScoHqrCEthYG92a74Mj8RmnwJdK7+A6eis+tuek+bt45Q+8ikFsJEMtrt3XONblj21ty2PGEIyCmUTRvMlSCTSTWpzzy1RpcIGi82QiZRB4dZjVnl8F0gR1yWMDx0xd27Nf7UhOn9i3oobMPjsqo/+yUYEXyRdNyBg0x5dwZQy+MSwxN52y3A8zxarUDVsiLUtulSWI+dNjHL4yatnQEw189Vy6XHxubtRnoQtr43fK9vd6xesWgsNrYvrI3/SEoHcw5Qa2B4sAZcAvjIcTQwTA818qQwsntHUZHv/xARnr4y0QSRVgohiPmLkhmDT5qGnK7GVXzrlyjuNeHtffdRPxu8ReCiicMmA0i/2EmdeAiwx90t2N44EiiXccy/2MaomcNUGqbn3wlhm96LNI3O27zrYCbMiuTc2v1a5HWav+beFZ/xNXovXY4KTdWPddxzmXKwjWLUXrbxPyqfeHtELKU4/0ccnTPzVk/hjFxWs7U9cVxgb/8YpzTn+4EUPw7B+NbTMvxCzg0h9tE9mRXCMYkWOZXT3G4R35FzEGoMsKOMqBPJidROM3LWwbYGdn3sXzM701Ef5T4EI1uCG26DjtBuYEtHe9l8kuQtMlmOVs1k+uuegcWTLU6e3N66/8vIznXOWLYZoRKuP8J/I9j8CDACvnsG0rktRxAAAAABJRU5ErkJggg==" />
		</div>
	</div>
	<div class="wrap">
		<h1>DotKernel System Requirements Check</h1>
		<p class="intro">
			We have run the tests below to determine if DotKernel will work in
			your environment.<br /> If any of the tests have failed, consult the
			<a href="http://www.dotkernel.com/docs/system-requirements/"
				target="_blank"> system requirements</a> page for more information.
		</p>
<?php
if($test)
{
	?>
	<div class="status_ok">
			<img alt="" width="54" height="54" border="0"
				src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADYAAAA2CAYAAACMRWrdAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABMpJREFUeNrUmm1oFEcYxx8vIan0zOWSppqcxnevWq0vEY0mEPFTRRIQI4rBYhDNN21rEy9NVWyjudpWwS8iCAZLA0UxIH4QQVFyChZESaCYxhqTGClU8l5aA6V9nr1ndWP2XnZm7vb2H/6QZO9m5rfzzDM7Mzul4qfVoFg+9MfoNWg/2o1ehs7g6+PoDvQYuhP9C/o6ul9lI9IVlUMN34KuQBfF+GyG4TNl6H38+wP0VXQrg0vJJfn9SvRjdDv6WBxQ0VTEZbRzmZV2gNGdvo++xOGmWn4u+z7XlXCwuehr6Ns8hhKtNVzXNa47IWAUGg/RmyH52sx1V6oGa+TQ8IB98nAbGlWApaHPoxsgddTAbUqTSfdUwG5IPe1hsGqRHmtKUShdu7mNlsC2owOQ+gpwW+MCo7R6Dpyjc2ZTgRnYBZuzn0i2vBALbKfoTG+zyrjtpmAZ0QajA9RkWEFMANuFLnQwWCEzTAILgPMVeBuMYnSBnS2an7MYzpZfgYtbb8CKGWtFi1mg5wiXIQxt0zyvHw6u/wYKps0CT6YXPlv/NazMLxYtbpcRrMIuqLneRVBbegJ8WbNf/y/7nRz4dN0xWJW/DtJclhf5W3QwWtbn2dVTtSUnsKcm5yyCqy1tgqqPaqwWm0NMdDs22gZVag6l66/xURj6Z0Ck+I3p3GPJH1MljVGhno88g+9CX8KzoS6RKlakq9qzmII//+FP7Oz3AXxRcjwqVP9ID3x/VxhKy440xnyyUAtzl8DhDadh6fSiGIliIWa/xphQJ0P10D3YJdMkn0s2cczOnq9lsKKCEjhQfBQW5S6NmP3qSoMTsp9Z+BGURE/pyiMwt0yqpsbOzJqj/f3+u/lamC3OWz5p8o2U/XT1DXfLhp9RbuENU/97y+BznFR1KF3T3QVaD9J14+QbradejPbCD/e+kg0/o8YpeYyJ9Nq2D6uh0DPP9NoMtw/2Fx+BK79ehHL/jphQwbY66Bn6XWXiHUvzby3Yi794rX6zb6Rb6xXv1FzT61mZ2bB2ZlnE62/CrwHD74nqGeUPCkWhU47uwd+kwufFaJ8GReUkQP0EJny76I6fDAUsw4W/d0hVojDTEwJ7JFNCeODHf+fp86fuHVaZKMz0iMBuyZaij5WeGGOFoL5tC8DTwc5EP7XdIjA6ZBuQLYkm12DboYg9EQ6/+kSGny5i6dDnsVYVJUYKS3pMshKukmo1LjR/VFXqmxTeNeExKcFjyiiNheYxrX4I78vlqCh55NUQdL5sh2mZHmh+eAbH1ONkQdEgP2gEo/XGK3S5qhpogXi39yYM/P1nMpd69RA+pJ+w/dYMil9JSLL6mQHeBqP3L+ocDFbHDJPASC3okAOhQtx2iARGolPCYQdBDYPJyaYrQmapcRBYjdnzbqSF5s/ooAOggtxWiBdMT50tKQzVwm0Eq2CkT4wpNIXUzG0DUbB/eWAGUyz8qrltwmDGsNzG+yN2aYzbUB/Ph63sUl1Gr4TwS5PJ1nWu+3K8X7C6/UZpdRN6g/5MlmA94Lo2Wd3CEN1XvIOmd26rZPZMYtzAKq7jjkgBsq/OtrBp054OD+nQTfT0poMXiVdVREO6wpAhH4Xw6X0FA5LdJrD6y84dbILpVdnl/wswAOhWa3L1/r26AAAAAElFTkSuQmCC" />
			<p>Your enviroment passed all the requirements needed to run
				DotKernel.</p>
		</div>
	<?php
}
else
{
	?>
	<div class="status_not_ok">
			<img alt="" width="54" height="54" border="0"
				src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADYAAAA2CAYAAACMRWrdAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABWZJREFUeNrUml1oE1kUx09m8tG09YOt9YNCYUtxH7RaaVFfJH2S1WJR6bqyi1Wp4oPK1k9SWr+w0kr9iD4pFFopDaxdCIhIn2QXfNmFump8WSj7sFL8qBYKJU0rSfacyZ04bSeZuXcmJjnwh0By75zfnDsn58y9jqmNG8Fmq0B9j9qM+g5ViqpBudn3c6gwahr1D+ov1Ahq3E4nHDaBkeN7UE2oOsE5RlGPUCEGnlOwZlQXi4ydRpHsRP0mOoEkOM6H+hM1nAUoYHMOs2v4vgbYt6jHqN/ZM5Rt28yu9ZhdOytgtOz+RjXC17dGdu1mu8G62NJYBrmzZcyHLjvAZFQfqgPyxzqYT3KmHzkNJqEJDkH+WSsDOywSse48hVLtEPORC+xHlB/y3/zMV1NglFbvQ+HYfb2/Aj2w/hxnP5Fs2W8E9pPoP32Ozcd81wVzZ3oYM5rbDd6rV6G4rw+kVau4h3uOHoWShw9B3rTJCly3poOYB3YAVSkEdfkyuHbtAmd9PXhv3QJp9WrzUK2t4Dl+HOS1a6G4txfk2lpRsErGsAiMPwt6POC9dAlcO3d++Udftw686KCjvNwc1MmTX1qNFSuScOIdh38hGK3Raq4pnE7wXrwIrsbFpaNcUwPFgUDGyHmOHJkHlYLDG1KMURdcltVqjpA0y9B8E7d0KXi7u3Wh5kXuxg3dZ06J1IkT6ecvK1Mi59yyRQTugBasiQtsyRJTF5XXr0/CaSKnRCoDlHZZUuQFbI8KRqPLeUbGx8chgs4lPnwwhkPnVDh3S0sSyuEwHDf34AHM9veLgH1DTPRq4Bf8EBCZQd6wQXke6O4a3oy3b0FauRIHyaagordvW0n9bWrEhCz26hVETp+GxMSEcX+0Zo15qEAALFqtZPWdRQru40fL5QMtPSVSiYTVqaoJrMLqLLFwGCJnzliCU56pu3ftKrEqJN7EkRbu5UuItLVB/N27XEZKtXICK7Vrttjr1zBz/jwkIhHzkRoehtk7d+wuikslu2ekcshhIkmkfl9VZar84l3ZBDZt12yu7duh6NQppYY0DVZXp5RmwHEzTNg0gU3YMZPT54Oizk7M6/yLwLltG3gvXMA747ILbIq8sLzLQeUV9WNUQwpHe/duKGpvF7oxOjZOs4xZhqI2xQJUqrXbuxeK/H47luUYgb0Qhtq6FbzXr5uC+hwKQfzNG2O4ffvsiNwLGv1UCKqhIRmp5ctN/fnOXLkCMx0dkPj0yRiuuVnpyh0lJaJgTwmMNtkmeUZR3ee9dk1pXwyhBgZStZ9SfmHWNNMVuJqawL1/vwgUsYTVeIe42pb372H23j3DSiFV0Gp+p8BR+WUQudjz5/D5yRMRsJC20RzkGhqPw9zgIESxz6LPaSOVpqJQassMXUFsdBQiZ88qrY6ADWrBnolkx7mhoSRcuuWXBjpVW+rAUaQi585BYnJSKBui/tCCxVC9QrVLMAhRTCLqcuPpp5TIUW3JugIlUuJQoGXQbq7Ty8Z/RdsY98GDIJWVQRQ7au76EssqyoTRmzettD5UaFRB8rjFolMD9Jp4CArTfkYFU5l7wZdB9rwVmj3TQumBkdEu4VQBQU2Bzs6mlCazHCsgsGN6GT1dQfYrqqcAoHqYr2AWjKx94brNMwsyH4EXjKwFNZCHUAPMNxAFi7EHsyfPlt9h5pswmHZZ/mDn+xGR9xjMh3ZTHQjHxHTEjjatRnIANcKubfqYH2+bSml1B6oBkgcns22j7Fo7eIt00f6bKuh6VsaMZQFojM1dr1brvGbX0Vk6Lkubh7TpJrp7E2ZN4iM7VoMjC4edKxlkDVOpDqx62DnMRDD/2enE/wIMAKdcnZ8LWVMpAAAAAElFTkSuQmCC" />
			<p>Failed</p>
		</div>
	<?php
}
?>
	<div class="req">
			<table>
				<tr>
					<td rowspan="10" valign="middle" width="45%">
						<h2>System Environment</h2>
					</td>
				</tr>
		<?php parseHtmlRows($checkServer); ?>
		</table>
		</div>
		<div class="req">
			<table>
				<tr>
					<td rowspan="5" valign="middle" width="45%">
						<h2>
							PHP <br> Extensions
						</h2>
					</td>
				</tr>
		<?php parseHtmlRows($check); ?>
		</table>
		</div>
		<div class="req">
			<table>
				<tr>
					<td rowspan="5" valign="middle" width="45%">
						<h2>
							PHP <br> Character Encoding
						</h2>
					</td>
				</tr>
		<?php parseHtmlRows($checkUtf8); ?>
		</table>
		</div>
		<div class="req">
			<table>
				<tr>
					<td rowspan="7" valign="middle" width="45%">
						<h2>
							PHP <br> Optional Extensions
						</h2>
					</td>
				</tr>
		<?php parseHtmlRows($checkOptional); ?>
		</table>
		</div>

	</div>
</body>
</html>