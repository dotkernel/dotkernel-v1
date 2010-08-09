<?php 
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotKernel
 * @copyright  Copyright (c) 2009-2010 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */
 
 /**
 * Check DotKernel Requirements for Installation 
 * @author     DotKernel Team <team@dotkernel.com>
 */

$test = true;
// check APACHE VERSION
$checkServer['APACHE Version'] = array('status' => 'Pass', 'value' => apache_get_version());
// check PHP VERSION
if(version_compare(PHP_VERSION, '5.2.10', '>='))
{
	$checkServer['PHP Version'] = array('status' => 'Pass', 'value' => PHP_VERSION);
}
else
{
	$checkServer['PHP_VERSION'] = array('status' => 'Failed', 'value' => 'DotKernel requires <a href="http://php.net/downloads.php">PHP</a> 5.2.10 or newer, your version is '.PHP_VERSION.'.');
	$test = false;
}
// check MySQL Client version
if(function_exists('mysqli_get_client_version') && function_exists('mysqli_get_client_info') )
{
	$mysqlVersion =  mysqli_get_client_version(); #for version 4.1.6 return 40106;
	#create mysql version string to check it
	$mainVersion = (int)($mysqlVersion/10000);
	$a = $mysqlVersion - ($mainVersion*10000);
	$minorVersion = (int)($a/100);
	$subVersion = $a - ($minorVersion*100);
	$mysqlVersion = $mainVersion.'.'.$minorVersion.'.'.$subVersion;
	$mysqlClientVersion = current(explode(' - ',mysqli_get_client_info()));
	if(version_compare($mysqlVersion, '5.0', '>='))
	{
		$checkServer['MySQL Client Version'] = array('status' => 'Pass', 'value' => $mysqlClientVersion);
	}
	else
	{
		$checkServer['MySQL Client Version'] = array('status' => 'Failed', 'value' => 'DotKernel requires <a href="http://dev.mysql.com/downloads/">MySQL</a> 5.0 or newer, your version is '.$mysqlClientVersion.'.');
		$test = false;
	}
}
else
{
	$checkServer['MySQL Client Version'] = array('status' => 'Failed', 'value' => 'DotKernel requires that your PHP enviroment have <b>MySQLi</b> extension enabled.');
	$test = false;
}
// check Zend Framework version
$zendExists = @include_once 'Zend/Loader/Autoloader.php';
if($zendExists)
{
	$zend_loader = Zend_Loader_Autoloader::getInstance();
	if(version_compare(Zend_Version::VERSION, '1.8.0', '>='))
	{
		$checkServer['Zend Framework Version'] = array('status' => 'Pass', 'value' => Zend_Version::VERSION);
	}
	else
	{
		$checkServer['Zend Framework Version'] = array('status' => 'Failed', 'value' => 'DotKernel requires <a href="http://framework.zend.com/download">Zend Framework</a> 1.8.0 or newer, your version is '.Zend_Version::VERSION.'.');
		$test = false;
	}
}
else
{
	$checkServer['Zend Framework Version'] = array('status' => 'Failed', 'value' => 'DotKernel requires that <a href="http://framework.zend.com/download">Zend Framework</a> be installed on your server. </br>Check this article <a href="http://www.dotkernel.com/zend-framework/zend-framework-pear-plesk-server/">Zend Framework as PEAR accessible repository on Plesk server</a> for more details on how to install it.');
	$test = false;
}

// check apache module rewrite
if(in_array('mod_rewrite', apache_get_modules()))
{
	$check['Apache <i>mod_rewrite</i> module'] = array('status' => 'Pass', 'value' => 'OK');
}
else
{
	$check['Apache <i>mod_rewrite</i> module'] = array('status' => 'Failed', 'value' => 'DotKernel requires Apache mod_rewrite for htaccess route.');
	$test = false;
}
// check ctype	
if(extension_loaded('ctype'))
{
	$check['PHP <i>Ctype</i> extension'] = array('status' => 'pass', 'value' => 'OK');
}
else
{
	$check['PHP <i>Ctype</i> extension'] = array('status' => 'failed', 'value' => 'DotKernel requires <a href="http://www.php.net/manual/en/book.ctype.php">Ctype</a>, used by Zend Framework.');
	$test = false;
}
// check PDO MySQL
if(extension_loaded('pdo_mysql'))
{
	$check['PHP <i>PDO-MySQL</i> Extension'] = array('status' => 'pass', 'value' => 'OK');
}
else
{
	$check['PHP <i>PDO-MySQL</i> Extension'] = array('status' => 'failed', 'value' => 'DotKernel requires <a href="http://www.php.net/manual/en/ref.pdo-mysql.php">PDO MySQL</a> for database connection. ');
	$test = false;
}
// check session
if(extension_loaded('session'))
{
	$check['PHP <i>session</i> extension'] = array('status' => 'pass', 'value' => 'OK');
}
else
{
	$check['PHP <i>session</i> extension'] = array('status' => 'failed', 'value' => 'DotKernel requires <a href="http://www.php.net/manual/en/book.session.php">session</a>, used by Zend Framework. ');
	$test = false;
}
// check SPL
if(function_exists('spl_autoload_register'))
{
	$check['PHP <i>SPL</i> extension'] = array('status' => 'pass', 'value' => 'OK');
}
else
{
	$check['PHP <i>SPL</i> extension'] = array('status' => 'failed', 'value' => 'DotKernel requires <a href="http://www.php.net/manual/en/book.spl.php">SPL</a>, used by Zend Framework. ');
	$test = false;
}

// check cURL
if(extension_loaded('curl'))
{
	$checkOptional['PHP <i>cURL</i> extension'] = array('status' => 'pass', 'value' => 'OK');
}
else
{
	$checkOptional['PHP <i>cURL</i> extension'] = array('status' => 'failed', 'value' => 'DotKernel requires <a href="http://www.php.net/manual/en/book.curl.php">cURL</a> for the Dot_Curl class.');
}
// check gd
if(extension_loaded('gd'))
{
	$checkOptional['PHP <i>gd</i> extension'] = array('status' => 'pass', 'value' => 'OK');
}
else
{
	$checkOptional['PHP <i>gd</i> extension'] = array('status' => 'failed', 'value' => 'DotKernel requires <a href="http://www.php.net/manual/en/book.image.php">gd</a>, used by Zend Framework.');
}
// check mbstring
if(extension_loaded('mbstring'))
{
	$checkOptional['PHP <i>mbstring</i> extension'] = array('status' => 'pass', 'value' => 'OK');
}
else
{
	$checkOptional['PHP <i>mbstring</i> extension'] = array('status' => 'failed', 'value' => 'DotKernel requires <a href="http://www.php.net/manual/en/book.mbstring.php">mbstring</a>, used by Zend Framework.');
}
//check GeoIp
if(extension_loaded('geoip'))
{
	$checkOptional['PHP <i>GeoIP</i> Extension'] = array('status' => 'pass', 'value' => 'OK');
}
else
{
	$checkOptional['PHP <i>GeoIP</i> Extension'] = array('status' => 'failed', 'value' => 'DotKernel requires <a href="http://www.php.net/manual/en/book.geoip.php">GeoIP</a> used by the Dot_GeoIP class for faster compilation.');
}
?>

<!-- Start to parse the installation requirements HTML format-->
<html>
<head>
	<title>DotKernel | Installation | System Requirements </title>
<style type="text/css">
	div{
		width: 750px;
		padding: 5px 15px 5px 5px;
		border: 1px solid #8c8c8c;
		background: #fcfcfc;
	}
	table{
		margin: 5px;
		height: 24px;
		line-height: 24px;
		border: 1px solid #8c8c8c;
		color: #000000;
		}
	
	.pass {
		color: #00aa00;
		padding: 0px 5px 0px 5px;
	}
	.failed{
		color: #cc0000;
		padding: 0px 5px 0px 5px;
	}
	.row1{
		background: #efefef;
	}
	.row2{
		background: #ffffff;
	}
	.testpass{
		color: #f4fff4;
		background: #00aa00;
		padding: 10px;
		border: 1px solid #efefef;
		font-size: 14px;
	}
	.testpass p{
		font-size: 28px;
		font-family: Tahoma, sans-serif;
		line-height: 32px !important;
	}
	

</style>
</head>
<body>
<div>

<h2>DotKernel System Requirements</h2>
<p>
	We have run the below tests to determine if DotKernel will work in your environment. If any of the tests have failed, consult the <a href="http://www.dotkernel.com/docs/system-requirements/">system requirements</a> page for more information. 
</p>
<h3>Enviroment Test</h3>
<table cellpadding="2" cellspacing="2" width="100%">
<?php
$bg = 0;
foreach($checkServer as $ky => $val)
{
	?>
		<tr class="row<?php echo ($bg++)%2+1?>">
			<td width="30%"> <?php echo $ky?> </td>
			<td width="70%" class="<?php echo $val['status'] ?>"><?php echo $val['value'] ?> </td>
		</tr>
	<?php
}

?>
</table>
<br>
<h3>Extensions Test</h3>
<table cellpadding="2" cellspacing="2" width="100%">
<?php
$bg = 0;
foreach($check as $ky => $val)
{
	?>
		<tr class="row<?php echo ($bg++)%2+1?>">
			<td width="30%"> <?php echo $ky?> </td>
			<td width="70%" class="<?php echo $val['status'] ?>"><?php echo $val['value'] ?> </td>
		</tr>
	<?php
}

?>
</table>
<br>
<?php
if($test)
{
	?>
	<div class="testpass"><p>Your enviroment passed all the requirements needed to run DotKernel.</p>
	You can now delete or rename the <i>install.php</i> file.</div>
	<?php
} 
?>
<h3>Optional Extensions Test</h3>
<p>The following extensions are optional for DotKernel, but if used can provide access to more classes. </p>
<table cellpadding="2" cellspacing="2" width="100%">
	<?php
foreach($checkOptional as $ky => $val)
{
	?>
		<tr class="row<?php echo ($bg++)%2+1?>">
			<td width="30%"> <?php echo $ky?> </td>
			<td width="70%" class="<?php echo $val['status'] ?>"><?php echo $val['value'] ?> </td>
		</tr>
	<?php
}

?>
</table>
</div>
</body>
</html>

