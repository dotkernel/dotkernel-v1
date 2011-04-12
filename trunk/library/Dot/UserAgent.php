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
	private $_cache;
  /**
	 * Constructor, set the WURFL configuration paramethers
	 * @access public
	 * @return Dot_UserAgent
	*/
	function __construct()
	{		
		$this->config = Zend_Registry::get('configuration');

	 // load WURFL library and configuration XML file
		require_once $this->config->resources->useragent->wurflapi->lib_dir . 'Application.php';
	  $wurflConfig = new WURFL_Configuration_XmlConfig($this->config->resources->useragent->wurflapi->config_file);
		// load the confiuration object in a class available variable 
	  $this->wurflConfig = $wurflConfig;
		
		// if we have APC enabled, start the cache object too 
		if(TRUE == $this->config->resources->useragent->wurflapi->cache && 
		    function_exists('apc_cache_info') && (@apc_cache_info() !== FALSE) )
		{			
			// start the cache factory
			$frontendOptions = array('lifetime' => $this->config->resources->useragent->wurflapi->cache_lifetime ,               
											 				  'automatic_serialization' => TRUE );
			$cache = Zend_Cache :: factory('Core', 'APC', $frontendOptions);
			$this->_cache = $cache;
		}	
		
		// start the WURLF object
		$wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);
		$this->wurflManagerFactory = $wurflManagerFactory;
	}
	
	/**
	 * Create the WURFL object and store the content. Use with precaution !!! As will load a lot of files
	 * @access public
	 * @return void
	 */
	public  function createWurflFactory()
	{
		$this->wurflManagerFactory->create(true);
	}
	
	/**
	 * Remove the content from the persistent storage 
	 * @access public
	 * @return void
	 */
	public function removeWurflFactory()
	{
		$this->wurflManagerFactory->remove();
	}
	
	/**
	 * Get information about WURFL  
	 * @access public
	 * @return bidimensional array, with version and lastUpdated
	 */
	public function getWurflVersion()
	{
		//We need to try to create it again, but if already exists , is not created again 
		$wurflManager = $this->wurflManagerFactory->create(true);
		
		// alternate load ( WURFL_Xml_Info::PERSISTENCE_KEY )
		//  const PERSISTENCE_KEY = "WURFL_XML_INFO";
		
		$info = $wurflManager->getWURFLInfo();
		$wurflInfo['version']     = $info->version;
		$wurflInfo['lastUpdated'] = $info->lastUpdated;

		return $wurflInfo; 
	}
	
	/**
	 * Return a hige un-usable object . Use it with precaution !
	 * @param object $server where is usually an object similar with $_SERVER global variable
	 * @access private , we don't want to be called within controller, is too expensive
	 * @return object
	 */
	private function _getDeviceForHttpRequest($server)
	{
		$wurflManager = $this->wurflManagerFactory->create(true);
		return $wurflManager->getDeviceForHttpRequest($server);
	}
	
	/**
	 * Return a hige un-usable object . Use it with precaution !
	 * @param string UserAgent, usually $_SERVER["HTTP_USER_AGENT"]
	 * @access private , we don't want to be called within controller, is too expensive
	 * @return object
	 */
	private function _getDeviceForUserAgent($userAgent)
	{
		$wurflManager = $this->wurflManagerFactory->create(true);
		return $wurflManager->getDeviceForUserAgent($userAgent);
	}
	
	/** 
	 *  Return a nice usable array with device capabilities
	 * @access public 
	 * @param string $userAgent
	 * @return  array
	 */
	public function getDeviceCapabilities($userAgent)
	{
		return $this->_getDeviceForUserAgent($userAgent)->getAllCapabilities();
	}
	
	/** get from WURFL huge object only few interesting params
	 * @access public
	 * @param object $userAgent
	 * @return array with short summary related to device 
	 */		
	public function getDevice($userAgent)
	{
		// if cache is enabled in application.ini and APC is enabled 
		if($this->_cache)
		{
			$cacheToken = $this->config->resources->useragent->wurflapi->cache_namespace . '_' . md5($userAgent);						
			//check if we have the info there in cache 
			$device = $this->_getDeviceFromCache($cacheToken);	
			if($device)
			{
				return $device;				
			}
			else
			{
				// prepare the array 
			 	$device = $this->_prepareDeviceInfo($userAgent);
			 	//save the array in cache 
				$this->_saveDeviceToCache($device, $cacheToken);
				return $device;
			}
				
		}	
		// No APC cache available
		else
		{
			return $this->_prepareDeviceInfo($userAgent);
		}		
	}
	
	/**
	 * Core function. Prepare the device short info
	 * @access private
	 * @param object $userAgent
	 * @return array
	 */
	private function _prepareDeviceInfo($userAgent)
	{
		// prepare the array with device info
		$deviceCapabilities = $this->getDeviceCapabilities($userAgent);
		
		$device['fallBack']       = $this->getDeviceFallBack($userAgent);
		$device['brandName']       = $deviceCapabilities['brand_name'];
		$device['productName']     = $deviceCapabilities['marketing_name']; // THIS IS STRANGE !!!!
		$device['modelName']       = $deviceCapabilities['model_name'];
		$device['browserName']     = $deviceCapabilities['mobile_browser'];
		$device['browserVersion']  = $deviceCapabilities['mobile_browser_version']; 
		$device['deviceOs']        = $deviceCapabilities['device_os'];
		$device['deviceOsVersion'] = $deviceCapabilities['device_os_version']; 
		$device['isTablet']        = is_null($deviceCapabilities['is_tablet']) ? '1' : '0';; 
		$device['isDesktop']       = is_null($deviceCapabilities['mobile_browser'])? '1' : '0';
		$device['isMobile']        = is_null($deviceCapabilities['mobile_browser'])? '0' : '1';
		$device['isSmartphone']    = $this->isSmartphone();
		$device['isIphone']        = $this->_isIphone($device['fallBack']);
		$device['isAndroid']       = $this->_isAndroid($device['deviceOs']);
		$device['isBlackberry']    = $this->_isBlackberry($device['fallBack']);	
		return $device;		
	}
	
	/**
	 * Return device fall back, for instance the string 'apple_iphone_ver3_1_2'
	 * @param object $userAgent
	 * @return 
	 */
	public function getDeviceFallBack($userAgent)
	{
		return $this->_getDeviceForUserAgent($userAgent)->fallBack;
	}
	
	/**
	 * Retrieve from cache a serializes array with device infos. Called only when we have the _cache set
	 * @access private
	 * @param object $cacheToken
	 * @return 
	 */
	private function _getDeviceFromCache($cacheToken)
	{
		$device = FALSE;
		// check if we have a that cache piece 
		if($this->_cache->test($cacheToken))
		{
			$device = $this->_cache->load($cacheToken);
		}		
		return $device;
	} 
	
	/**
	 * Save into the cache the device info array 
	 * @param object $device , $cacheToken
	 * @return void
	 */
	private function _saveDeviceToCache($device, $cacheToken)
	{
		$this->_cache->save($device, $cacheToken);
	}
	public function getDeviceType($userAgent)
	{
	 //return 'mobile', 'desktop', 'bot', 'console', 'email', 'feed', 'offline', 'probe', 'spam'
	}
	
	function isSmartphone() {}
	
	/**
	 * Check again device fallBack the string iphone
	 * @access private
	 * @return bool
	 */
	private function _isIphone($deviceFallBack)
	{
		$isIphone = 0;
		if(stripos($deviceFallBack, 'iphone') !== FALSE)
		{
			$isIphone = '1';	
		}
		return $isIphone;
	}
	
	/**
	 * Check again device OS Name the string Android
	 * @access private
	 * @return bool
	 */
	private function _isAndroid($deviceOsName)
	{
		$isAndroid = 0;
		if(stripos($deviceOsName, 'Android') !== FALSE)
		{
			$isAndroid = '1';	
		}
		return $isAndroid;
	}
	
	/**
	 * Check again device fallBack the string Blackberry
	 * @access private
	 * @return bool
	 */
	private function _isBlackberry($deviceFallBack) 
	{
		$isBlackberry = 0;
		if(stripos($deviceFallBack, 'Blackberry') !== FALSE)
		{
			$isBlackberry = '1';	
		}
		return $isBlackberry;
	}
	
	#TODO isSOMETHING functions must return boulean 
	#TODO write only once the data in Storage, and only from admin 
	#TODO data in Storage must be very summary, only strictly necessary informations, and presented as a Big Array
	#TODO data Storage in cache/wurfl folder , as 1 or 2 files, and moved when necessary in APC or memcached
	#TODO read-only  data by the other functions and controllers
	#TODO store in session 	only when is necessary, in Controller
	#TODO Dk articles about that strange experience 
	#TODO move here function getDevice from Dot_Kernel.php and everywhere where is called
}