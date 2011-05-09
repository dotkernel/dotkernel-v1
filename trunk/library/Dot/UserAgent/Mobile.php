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
* Mobile Devices related functions
* @category   DotKernel
* @package    DotLibrary
* @subpackage DotUserAgent
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_UserAgent_Mobile extends Dot_UserAgent
{
	/**
	 * Constructor
	 * @access public
	 * @return Dot_Kernel
	 */
  public function __construct()
	{
		$this->db = Zend_Registry::get('database');
	}	
	
	/**
	 * Register mobile hits, insert into mobileHit table
	 * @access public
	 * @return void
	 */
	public function registerHit()
	{
		// prepare mobileHit data
		$ip = Dot_Kernel::GetUserIp();
		
		$dotGeoip = new Dot_Geoip();
		$country = $dotGeoip->getCountryByIp($ip);
		
		$wurflDetails = parent::getDevice();
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
		$mobileHit = array('ip' => $ip,
							'device' => $device ,
							'deviceModel' =>  $deviceModel,
							'carrier' => $this->_getCarrier(),
							'operatingSystem' => $operatingSystem,
							'browser' => $browser,
							'country' => $country[1],
							'userAgent' => $userAgent,
							'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''
							);
		$this->db->insert('mobileHit', $mobileHit);
	}
	
	/**
	 * Analise the IP and get the carrier : AT&T, Wifi, etc.
	 * @todo to be developed
	 * @access private
	 * @return string
	 */
	private function _getCarrier()
	{
		return 'unknown';
	}
}
