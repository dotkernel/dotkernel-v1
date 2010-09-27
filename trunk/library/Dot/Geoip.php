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
* Geo IP related stuff
* @category   DotKernel
* @package    DotLibrary
* @subpackage DotGeoip
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Geoip 
{
	/**
	 * Constructor
	 * @access public
	 * @return dot_Geoip
	 */
	public function __construct()
	{		
		$this->config = Zend_Registry::get('configuration');
	}	
	/**
	 * Get the country by IP
	 * Return an array with : short name, like 'us' and long name, like 'United States'
	 * @access public 
	 * @param string $ip
	 * @return array
	 */
	public function getCountryByIp($ip)
	{
		if(extension_loaded('geoip') == FALSE)
		{
			// GeoIp extension is not active
			$api = new Dot_Geoip_Country();
			$geoipPath = 'externals/geoip/GeoIP.dat';
			if(file_exists($geoipPath))
			{
				$country = $api->getCountryByAddr($geoipPath, $ip);
			}
			else
			{
				die('<b>mod_geoip</b> extension is not installed. 
				<br /> Check if <b>externals/geoip/GeoIP.dat</b> file exists. <br /> 
				If not, download it from <a href="http://www.maxmind.com/app/geolitecountry">
				http://www.maxmind.com/app/geolitecountry</a>');
			}
		}
		else
		{
			$country[0] = geoip_country_code_by_name ($ip);
			$country[1] = geoip_country_name_by_name($ip);
		}		
		return $country;
	}	
}
