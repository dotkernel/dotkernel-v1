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
		$check['apache_mod_rewrite'] = array('name'   => 'Apache <i>mod_rewrite</i> module', 
											                   'status' => 'failed', 
											                   'value'  => 'DotKernel requires Apache mod_rewrite for htaccess route.');
		$test = false;
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
	$bg = 0;
	foreach($data as $ky => $val)
	{
		?>
			<tr class="row<?php echo ($bg++)%2+1?>">
				<td width="30%"> <?php echo $val['name'];?> </td>
				<td width="70%" class="<?php echo $val['status']; ?>"><?php echo $val['value']; ?> </td>
			</tr>
		<?php
	}
}
?>

<!-- Start to parse the installation requirements HTML format-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" 
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<!-- Start to parse the installation requirements HTML format-->

	<title>DotKernel System Requirements Check</title>

<style type="text/css">
	body{
		text-align: center;
		background-color: #0D2129;
		font-family: Lucida sans unicode, sans-serif;
		font-size: 13px;
		padding: 0px;
		margin: 20px 0px;
	}
	h1{
		font-size: 28px;
		font-weight: bold;
		font-family: Tahoma, sans-serif;
		color: #EE6C25;
		
	}
	h2{
		font-size: 17px;
		font-weight: bold;
		font-family: Tahoma, sans-serif;
		color: #EE6C25;
		
	}
	h3{
		font-size: 15px;
		font-weight: bold;
		font-family: Tahoma, sans-serif;
		color: #EE6C25;
	}
	a{
		color: #0F83C8;
		text-decoration: none;
	}
	a:hover{
		color: #EE6C25;
		text-decoration: underline;
	}
	p{
		padding: 0px;
		margin: 10px 0px;
	}
	input,textarea,select{
		padding: 5px;
		font-size: 13px;
		border: 1px solid #A3A3A3;
		margin: 5px 0px 5px;
		vertical-align: middle;
		font-family: Lucida sans unicode, sans-serif;
	}
	select{
		padding: 0px;
	}
	input[type=radio] { border: 0px; }
	input[type=checkbox] { border: 0px; }

	.submit_button{
		background: #2072AE;
		padding: 5px 10px 5px 10px;
		text-align: center;
		color: #ffffff;
		font-size: 13px;
		text-transform: uppercase;
		border: 0px;
		vertical-align: middle;
		cursor: pointer;
	}
	.wrap{
		margin: 0px auto;
		width: 750px;
		padding: 5px 35px 35px 35px;
		border: 1px solid #000000;
		background: #FCFCFC;
		text-align: left;
	}
	.intro{
		margin: 0px 0px 30px 0px;
		font-size: 16px;
		line-height: 24px;
		padding: 15px 20px;
		background-color: #F8F1E4;
		color: #603E28;
	}
	fieldset{
		padding: 0px;
		margin: 30px 0px 0px 0px;
		border: 0px;
		border-top: 2px solid #F58625;
	}
	legend{
		font-size: 19px;
		font-weight: bold;
		font-family: Tahoma, sans-serif;
		color: #EE6C25;
		padding: 0px 10px 0px 0px;
		margin: 0px 0px 3px 0px;
	}
	table{
		margin: 10px 0px 0px 0px;
		background: #D8E0E7;
		line-height: 24px;
		color: #4A4A4A;
		}
	table td{
		padding: 4px 10px 4px 10px;
		background: #FBFCFD;
		line-height: 24px;
	}
	.pass {
		color: #12A30E;
		font-weight: bold;
	}
	.failed{
		color: #B8250C;
		font-weight: bold;
	}
	.note{
		margin: 5px 0px 0px 0px;
		color: #747474;
	}
	.testpass{
		margin: 30px 0px 20px 0px;
		color: #5F704B;
		background: #DBF3D8;
		padding: 20px;
		font-size: 14px;
	}
	.testpass p{
		margin: 0px 0px 10px 0px;
		padding: 0px;
		font-size: 22px;
		color: #1B7A0A;
		font-weight: bold;
		font-family: Tahoma, sans-serif;
		line-height: 29px !important;
	}		
	.testfailed{
		margin: 30px 0px 20px 0px;
		color: #FFFFFF;
		background: #B8250C;
		padding: 20px;
		font-size: 14px;
	}
	.testfailed a{
		color: #3300FF;
	}
	.testfailed p{
		margin: 0px 0px 10px 0px;
		padding: 0px;
		font-size: 22px;
		color: #FFFFFF;
		font-weight: bold;
		font-family: Tahoma, sans-serif;
		line-height: 29px !important;
	}
	.contact_form{
		list-style-type: none;
		padding: 0px;
		margin: 0px;
	}
	.contact_form li{
		padding: 0px;
		margin: 0px 0px 10px 0px;
	}
	.contact_form label{
		display: block;
		padding-top:5px;
		float: left;
		width: 120px; /* increase or descrease the width in regard to the lenght of the labels*/
	}
	.contact_form input{
		margin: 0px;
		width: 200px;
	}
	.contact_form textarea{
		width: 400px;
	}
	.contact_form .submit_button{
		width: auto;
	}
</style>
</head>

<body>
<div class="wrap">
	<h1>DotKernel  System Requirements Check</h1>
	<p class="intro">
		We have run the below tests to determine if DotKernel will work in your
	 environment. If any of the tests have failed, consult the <a href="http://www.dotkernel.com/docs/system-requirements/">system requirements</a> page for more information. 
	</p>
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
<?php
if($test)
{
	?>
	<div class="testpass"><p>Your enviroment passed all the requirements needed to run DotKernel.</p>
	You can now delete or rename the <i>dk.php</i> file.</div>
	<?php
} 
else
{
	?>
	<div class="testfailed"><p>The test has failed. </p>
	Check <a href="http://www.dotkernel.com/docs/system-requirements/">system requirements</a> page for more information. </div>
	<?php
}
?>
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
	
	<hr color='#E6E6E6' width= size=1>
	<h3>More styles</h3>
	<p>
		Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
	</p>
	<a href="">...read more</a>
		Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.<a href="">...read more</a> 
	<br>
	This is a button <input type="button" value="Press here" onclick="" class="submit_button"><br>
	This is a radio button <input type="radio" name=""> <input type="radio" name=""><br>
	This is a checkbox <input type="checkbox" name=""> <input type="checkbox" name=""> <input type="checkbox" name=""><br>
	This is a select 
	<select name="sfdsfd">
		<option value="sdfsdfasd" selected>
		<option value="sdfsdfsdf">
	</select> <br>
	
	This is a field <input type="text" name=""> <br>
	<hr color='#CACACA' width= size=1>
	<br>
	<h3>This is an example for a contact form</h3>
	
	  <ul class="contact_form">
		<li>
		  <label for="name">Name<em>*</em></label>
		  <input id="name" />
		</li>
		<li>
		  <label for="address1">Address<em>*</em></label>
		  <input id="address1" />
		</li>
		<li>
		  <label for="address2">Address 2</label>
		  <input id="address2" />
		</li>
		<li>
		  <label for="town-city">Town/City</label>
		  <input id="town-city" />
		</li>
		<li>
		  <label for="county">County<em>*</em></label>
		  <input id="county" />
		</li>
		<li>
		  <label for="postcode">Postcode<em>*</em></label>
		  <input id="postcode" />
		</li>
		<li>
		  <label for="postcode">Postcode<em>*</em></label>
		  <textarea name=""></textarea>
		</li>
		<li>
			 <label for="postcode">&nbsp;</label>
			<input type="button" value="Press here" onclick="" class="submit_button">
		</li>
	  </ul>

</div>
</body>
</html>