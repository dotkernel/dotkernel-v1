<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Log site visits
* @category   DotKernel
* @package    DotLibrary
* @subpackage DotStatistic
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Statistic_Visit extends Dot_Statistic
{
	/**
	 * Constructor
	 * @access public
	 * @return
	 */
  public function __construct()
	{
		$this->config = Zend_Registry::get('configuration');
	}

	/**
	 * Register usefull information about the visit
	 * @access public
	 * @return integer
	 */
	public static function recordVisit()
	{
		$db      = Zend_Registry::get('database');

		$ip      = Dot_Kernel::GetUserIp();
		$proxyIp = $_SERVER['REMOTE_ADDR'];

		$dotGeoip = new Dot_Geoip();
		$country = $dotGeoip->getCountryByIp($ip);

		$logVisit = array(
									'ip'               => $ip,
									'proxyIp'          => $proxyIp,
									'carrier'          => 'unknown',
									'country'          => $country[1],
									'accept'           => array_key_exists("HTTP_ACCEPT", $_SERVER) ?
																					$_SERVER["HTTP_ACCEPT"] : '',
									'acceptLanguage'   => array_key_exists("HTTP_ACCEPT_LANGUAGE", $_SERVER) ?
																					$_SERVER["HTTP_ACCEPT_LANGUAGE"] : '',
									'acceptEncoding'   => array_key_exists("HTTP_ACCEPT_ENCODING", $_SERVER) ?
																					$_SERVER["HTTP_ACCEPT_ENCODING"] : '',
									'acceptCharset'    => array_key_exists("HTTP_ACCEPT_CHARSET", $_SERVER) ?
																					$_SERVER["HTTP_ACCEPT_CHARSET"] : '',
									'userAgent'        => array_key_exists("HTTP_USER_AGENT", $_SERVER) ?
									 												$_SERVER["HTTP_USER_AGENT"] : '',
									'cacheControl'     => array_key_exists("HTTP_CACHE_CONTROL", $_SERVER) ?
																					$_SERVER["HTTP_CACHE_CONTROL"] : '',
									'cookie'           => array_key_exists("HTTP_COOKIE", $_SERVER) ?
																					$_SERVER["HTTP_COOKIE"] : '',
									'xWapProfile'      => array_key_exists("HTTP_X_WAP_PROFILE", $_SERVER) ?
																					$_SERVER["HTTP_X_WAP_PROFILE"] : '',
									'xForwardedFor'    => array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER) ?
																					$_SERVER["HTTP_X_FORWARDED_FOR"] : '',
									'xForwardedHost'   => array_key_exists("HTTP_X-FORWARDED-HOST", $_SERVER) ?
																					$_SERVER["HTTP_X-FORWARDED-HOST "] : '',
									'xForwardedServer' => array_key_exists("HTTP_X-FORWARDED-SERVER", $_SERVER) ?
																					$_SERVER["HTTP_X-FORWARDED-SERVER"] : '',
									'referer'          => array_key_exists("HTTP_REFERER", $_SERVER) ?
																					$_SERVER['HTTP_REFERER'] : ''
									);
		$db->insert('statisticVisit', $logVisit);
		return $db->lastInsertId();
	}

	/**
	 * Record mobile device info in database, for future analysis
	 * @param integer $visitId
	 * @param object $device
	 * @return void
	 */
	public static function recordMobileVisit($visitId, $device)
	{
		$db = Zend_Registry::get('database');
		$visitMobile = array ('visitId'         => $visitId,
													'fallBack'        => $device->fallBack,
													'brandName'       => $device->brandName,
													'modelName'       => $device->modelName,
													'browserName'     => $device->browserName,
													'browserVersion'  => $device->browserVersion,
													'deviceOs'        => $device->deviceOs,
													'deviceOsVersion' => $device->deviceOsVersion,
													'screenWidth'     => $device->screenWidth,
													'screenHeight'    => $device->screenHeight,
													'isTablet'        => (int)$device->isTablet,
													'isMobile'        => (int)$device->isMobile,
													'isSmartphone'    => (int)$device->isSmartphone,
													'isIphone'        => (int)$device->isIphone,
													'isAndroid'       => (int)$device->isAndroid,
													'isBlackberry'    => (int)$device->isBlackberry,
													'isSymbian'       => (int)$device->isSymbian,
													'isWindowsMobile' => (int)$device->isWindowsMobile
												);
		$db->insert('statisticVisitMobile', $visitMobile);
		return TRUE;
	}
}