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
		$this->option = Zend_Registry::get('option');
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
		$session = Zend_Registry::get('session');
		$country = array(0 => 'unknown',1 => 'NA');
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
				$session->message['txt'] = $this->option->warningModGeoIp;
				$session->message['type'] = 'warning';
			}
		}
		elseif(geoip_db_avail(GEOIP_COUNTRY_EDITION))
		{ 
			//if GeoIP.dat file exists
			$country[0] = geoip_country_code_by_name ($ip);
			$country[1] = geoip_country_name_by_name($ip);
		}		
		elseif(geoip_db_avail(GEOIP_CITY_EDITION_REV0))
		{	
			//if GeoIPCity.dat file exists
			$record = geoip_record_by_name($ip);
			if(!empty($record))
			{
				$country[0] = $record['country_code'];
				$country[1] = $record['country_name'];
			}
		}
		else
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
				$session->message['txt'] = $this->option->warningModGeoIp;
				$session->message['type'] = 'warning';
			}
		}
		return $country;
	}	
}