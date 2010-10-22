<?php 
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2010 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Bunch of miscelaneous  functions, used in all DotKernel Applications
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Mobile
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
		$ip = Dot_Kernel::GetUserIp();
		
		$dotGeoip = new Dot_Geoip();
		$country = $dotGeoip->getCountryByIp($ip);
		
		$wurflDetails = Dot_Kernel::getDevice();
		$mobileHit = array('ip' => $ip,
							'device' => $wurflDetails->getFeature('brand_name'),
							'deviceModel' =>  $wurflDetails->getFeature('marketing_name'),
							'carrier' => 'unknown',
							'operatingSystem' => $wurflDetails->getFeature('device_os'),
							'browser' => $wurflDetails->getFeature('browser_name'),
							'country' => $country[1],
							'userAgent' =>$wurflDetails->getUserAgent(),
							'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''
							);
		$this->db->insert('mobileHit', $mobileHit);
	}
}
