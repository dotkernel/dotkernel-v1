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
* Extract all informations from User Agent , including interaction with Luca's Passani WURFL library
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_UserAgent
{
	/**
	 * Constructor, 
	 * @access public
	 * @return Dot_UserAgent
	*/
	function __construct()
	{		
		$this->config = Zend_Registry::get('configuration');
	}
	
	/**
	 * Get HTTP UserAgent Device.
	 * Device may be Bot, Checker, Console, Desktop, Email, Feed, Mobile, Offline, Probe, Spam, Text, Validator
	 * @return object
	 */
	public static function getDeviceInfo($userAgent)
	{
		$wurfl = Dot_UserAgent_Wurfl::getInstance();
		$deviceInfo = $wurfl->getDevice($userAgent);
		return $deviceInfo;
	}
	
	/**
	 * Return the name of the browser icon based on User Agent
	 * @access public
	 * @static
	 * @param string $user
	 * @return string
	 */
	public static function getBrowserIcon($agent, $return = 'icon')
	{
		$xml = new Zend_Config_Xml(CONFIGURATION_PATH.'/browser.xml');
		$browser = $xml->name->type->toArray();
		foreach ($browser as $key => $val)
		{
			if (stripos($agent,$val['uaBrowser']) !== FALSE)
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
	 * @param string $user
	 * @return array
	 */
	public static function getOsIcon($agent)
	{
		$xml = new Zend_Config_Xml(CONFIGURATION_PATH.'/os.xml');
		$os = $xml->type->toArray();
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
						if(strstr($minor['uaString'],'|') !== FALSE)
						{
							$uaStringArray = explode('|',$minor['uaString']);
							foreach ($uaStringArray as $uaString)
							{
								if ((stripos($agent, $uaString) !== false))
								{
									$operatingSystem = array('icon'=>strtolower(str_replace(' ', '_', $osArray['os'])),
															 'major'=>$osArray['os'],
															 'minor'=>$minor['osName']);
									return $operatingSystem;
								}
							}
						}
						else
						{
							if ((stripos($agent, $minor['uaString']) !== false))
							{
								$operatingSystem = array('icon'=>strtolower(str_replace(' ', '_', $osArray['os'])),
											'major'=>$osArray['os'],
											'minor'=>$minor['osName']);
								return $operatingSystem;
							}
						}
					}
				}
				else
				{//no minor version known for this os
					if ((stripos($agent, $osArray['os']) !== false))
					{
						$operatingSystem = array('icon'=>strtolower(str_replace(' ', '_', $osArray['os'])),
									'major'=>$osArray['os'],
									'minor'=>'');
						return $operatingSystem;
					}
				}
			}
		}
		return array('icon'=>'unknown', 'major'=>'', 'minor'=>'');
	}
}