<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotLibrary
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * Log site visits
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotStatistic
 * @see		  Dot_Statistic
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
								'fallBack'        => isset($device->fallBack) ? $device->fallBack : '',
								'brandName'       => isset($device->brandName) ? $device->brandName : '',
								'modelName'       => isset($device->modelName) ? $device->modelName : '',
								'browserName'     => isset($device->browserName) ? $device->browserName : '',
								'browserVersion'  => isset($device->browserVersion) ? $device->browserVersion : '',
								'deviceOs'        => isset($device->deviceOs) ? $device->deviceOs : '',
								'deviceOsVersion' => isset($device->deviceOsVersion) ? $device->deviceOsVersion : '',
								'screenWidth'     => isset($device->screenWidth) ? $device->screenWidth : 0,
								'screenHeight'    => isset($device->screenHeight) ? $device->screenHeight : 0,
								'isTablet'        => isset($device->isTablet) ? (int)$device->isTablet : 0,
								'isMobile'        => isset($device->isMobile) ? (int)$device->isMobile : 0,
								'isSmartphone'    => isset($device->isSmartphone) ? (int)$device->isSmartphone : 0,
								'isIphone'        => isset($device->isIphone) ? (int)$device->isIphone : 0,
								'isAndroid'       => isset($device->isAndroid) ? (int)$device->isAndroid : 0,
								'isBlackberry'    => isset($device->isBlackberry) ? (int)$device->isBlackberry : 0,
								'isSymbian'       => isset($device->isSymbian) ? (int)$device->isSymbian : 0,
								'isWindowsMobile' => isset($device->isWindowsMobile) ? (int)$device->isWindowsMobile : 0
							);
		$db->insert('statisticVisitMobile', $visitMobile);
		return TRUE;
	}
}