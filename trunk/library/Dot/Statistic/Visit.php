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
	 * @return void
	 */
	public static function recordVisit($deviceInfo = NULL)
	{
		$db      = Zend_Registry::get('database');
		$session = Zend_Registry::get('session');		
		
		$ip      = Dot_Kernel::GetUserIp();
		$proxyIp = $_SERVER['REMOTE_ADDR'];
		
		$dotGeoip = new Dot_Geoip();
		$country = $dotGeoip->getCountryByIp($ip);
		
		$device = '';
		$deviceModel = '';
		$operatingSystem = '';
		$browser = '';
		
	/*	$wurflDetails = parent::getDevice();
		$device = is_null($wurflDetails->getFeature('brand_name')) ? '' : $wurflDetails->getFeature('brand_name');
		$deviceModel = is_null($wurflDetails->getFeature('marketing_name')) ? '' : $wurflDetails->getFeature('marketing_name');
		$userAgent = is_null($wurflDetails->getUserAgent()) ? $_SERVER["HTTP_USER_AGENT"] : $wurflDetails->getUserAgent();
		
		//if Wurfl operating system is null, read from configs/os.xml
		if(is_null($wurflDetails->getFeature('device_os')))
		{
			$os = parent::getOsIcon($_SERVER["HTTP_USER_AGENT"]);
			$operatingSystem = $os['minor'];			
		}
		else
		{
			$operatingSystem = $wurflDetails->getFeature('device_os');
		}
		//if Wurfl browser is null, read from configs/browser.xml
		if (is_null($wurflDetails->getFeature('browser_name'))) 
		{
			$browser = parent::getBrowserIcon($userAgent, 'browser');
		}
		else
		{
			$browser = $wurflDetails->getFeature('browser_name');
		}	
		*/	
		$logVisit = array(
									'ip'               => $ip,
		              'proxyIp'          => $proxyIp,
							    'device'           => $device ,
									'deviceModel'      => $deviceModel,
									'carrier'          => 'unknown',
									'operatingSystem'  => $operatingSystem,
									'browser'          => $browser,
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
    $session->visitId = $db->lastInsertId();
		return;
		//$registry->session->logVisitId = last insert id
	}
}