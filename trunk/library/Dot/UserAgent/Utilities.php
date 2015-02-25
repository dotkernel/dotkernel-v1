<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* A helper which extracts some safe & easy-detectable information from User Agent 
* This class provides User Agent Detection if there is no UserAgent Detector or
* the use of UserAgent Detector is not intended or needed 
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_UserAgent_Utilities
{

	/**
	 * @var array Collection of mobile browser keywords
	 */
	protected static $_mobileKeywords = array (
					'mobile', 'phone', // mobile devices
					'android', // android devices
					'samsung', // samsung native browser
					'nokia', // nokia native browser
					'opera mini', 'opera mobi', // opera mobile
					'blackberry', // blackberry phones
					'symbian', // symbian os
					' arm;', // arm cpu
					'ios', 'ipad', 'iphone', 'ipod',
					'fennec', // Firefox for mobile (codenamed Fennec)
					'htc', // htc devices
	);
	
	/**
	 * Check if the Device is Mobile
	 *
	 * This function was implemented to replace the WURFL Cloud integration
	 *
	 *
	 * @param unknown $userAgent
	 * @return boolean
	 */
	public static function isMobile($userAgent)
	{
		$userAgent = strtolower($userAgent);
		foreach(self::$_mobileKeywords as $key)
		{
			if(strpos($userAgent, $key) !== false)
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Browser List Array
	 * @var array
	 */
	private static $_browserList = null;
	/**
	 * OS List Array
	 * @var array
	 */
	private static $_osList = null;
	
	/**
	 * Get the Browser Array from private var or fetch it from cache or XML
	 * @access public
	 * @return array
	 */
	public static function getBrowserArray()
	{
		if(!is_null(self::$_browserList))
		{
			return self::$_browserList;
		}
		$registry = Zend_Registry::getInstance();
		
		$cacheKey = 'browser_xml';
		$value = Dot_Cache::load($cacheKey);
		if(false !== $value)
		{
			$browser = $value;
			self::$_browserList = $browser;
		}
		else
		{
			$xml = new Zend_Config_Xml(CONFIGURATION_PATH . '/useragent/browser.xml');
			$browser = $xml->name->type->toArray();
			Dot_Cache::save($value, $cacheKey);
			self::$_browserList = $browser;
		}
		return $browser;
	}
	
	/**
	 * Get the Browser Array from private var or fetch it from cache or XML
	 * @access public
	 * @return array
	 */
	public static function getOsArray()
	{
		if(!is_null(self::$_osList))
		{
			return self::$_osList;
		}
		$registry = Zend_Registry::getInstance();
		
		$cacheKey = 'os_xml';
		$value = Dot_Cache::load($cacheKey);
		if(false != $value )
		{
			$os = $value;
		}
		else
		{
			$xml = new Zend_Config_Xml(CONFIGURATION_PATH.'/useragent/os.xml');
			$os = $xml->type->toArray();
			self::$_osList = $os;
			Dot_Cache::save($os, $cacheKey);
		}
		return $os;
	}
	
	
	
	/**
	 * Return the name of the browser icon based on User Agent
	 * @access public
	 * @static
	 * @param string $userAgent
	 * @return string
	 */
	public static function getBrowserIcon($userAgent, $return = 'icon')
	{
		$browser = self::getBrowserArray();
		foreach ($browser as $key => $val)
		{
			if (stripos($userAgent,$val['uaBrowser']) !== false)
			{
				if('browser' == $return)
				{
					return $val['uaBrowser'];
				}
				return $val['uaIcon'];
			}
		}
		return 'unknown';
	}
	
	/**
	 * Return the name of the OS icon based on User Agent
	 * @access public
	 * @static
	 * @param string $userAgent
	 * @return array
	 */
	public static function getOsIcon($userAgent)
	{
		$os = self::getOsArray();
		foreach ($os as $major)
		{
			foreach ($major as $osArray)
			{
				if(array_key_exists('identify', $osArray))
				{//there are minor version
					// if we have only one menu, Zend_Config_Xml return a simple array, not an array with key 0(zero)
					if (!array_key_exists('0', $osArray['identify']))
					{
						//we create the array with key 0
						$osIdentify[] = $osArray['identify'];
					}
					else
					{
						$osIdentify = $osArray['identify'];
					}
					foreach ($osIdentify as $minor)
					{
						//check if there are different strings for detecting an operating system
						if(strstr($minor['uaString'],'|') !== false)
						{
							$uaStringArray = explode('|',$minor['uaString']);
							foreach ($uaStringArray as $uaString)
							{
								if ((stripos($userAgent, $uaString) !== false))
								{
									$operatingSystem = array('icon' => strtolower(str_replace(' ', '_', $osArray['os'])),
													'major'=>$osArray['os'],
													'minor'=>$minor['osName']);
									return $operatingSystem;
								}
							}
						}
						else
						{
							if ((stripos($userAgent, $minor['uaString']) !== false))
							{
								$operatingSystem = array( 'icon'=>strtolower(str_replace(' ', '_', $osArray['os'])),
												'major'=>$osArray['os'],
												'minor'=>$minor['osName']);
								return $operatingSystem;
							}
						}
					}
				}
				else
				{//no minor version known for this os
					if ((stripos($userAgent, $osArray['os']) !== false))
					{
						$operatingSystem = array( 'icon'=>strtolower(str_replace(' ', '_', $osArray['os'])),
										'major'=>$osArray['os'],
										'minor'=>'');
						return $operatingSystem;
					}
				}
			}
		}
		return array('icon' => 'unknown', 'major' => '', 'minor' => '');
	}
}