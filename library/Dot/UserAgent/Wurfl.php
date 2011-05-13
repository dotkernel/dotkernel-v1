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
* Dot User Agent Wurfl integration 
* @category   DotKernel
* @package    DotLibrary
* @subpackage DotUserAgent
* @author     DotKernel Team <team@dotkernel.com>
*/
	
class Dot_UserAgent_Wurfl
{
	// hold an instance of the class
	static $_instance;
	static  $wurflConfig;
	private $_cacheApc;
	private $_wurflManagerFactory;
	private $_versionFile;
	
	/**
	 * private constructor, set the WURFL configuration paramethers
	 * @return void
	 */
	 private function __construct()
	{
		$this->config = Zend_Registry::get('configuration');
		// load WURFL library and configuration XML file
		require_once $this->config->resources->useragent->wurflapi->lib_dir . 'Application.php';
		$wurflConfig = new WURFL_Configuration_XmlConfig($this->config->resources->useragent->wurflapi->config_file);
		self::$wurflConfig = $wurflConfig;
		
		// if we have APC enabled, start the cache object too 
		if(TRUE == $this->config->resources->useragent->wurflapi->cache && 
		    function_exists('apc_cache_info') && (@apc_cache_info() !== FALSE) )
		{			
			$frontendOptions = array('lifetime' => $this->config->resources->useragent->wurflapi->cache_lifetime ,               
											 				  'automatic_serialization' => TRUE );
			$cache = Zend_Cache :: factory('Core', 'APC', $frontendOptions);
			$this->_cacheApc = $cache;
		}	
		
		// Now is time to start the factory, if is a must ...
		if($this->_cacheExist()) $this->_buildFactory();
	}
	
	/**
	 * The singleton method 
	 * @return Dot_UserAgent_Wurfl instance
	 */
	public static function getInstance()
	{
		if(!(self::$_instance instanceof self))
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Start the WURFL library Factory Manager
	 * @return void 
	 */
	private function _buildFactory()
	{
		$wurflManagerFactory = new WURFL_WURFLManagerFactory(self::$wurflConfig);
		$this->_wurflManagerFactory = $wurflManagerFactory;
	}
	
	/**
	 * Important function, Decision if is allowed to start the wurflfactory object or not. 
	 * Is checking for version.txt file which is supposed to not be there if there is no cache 
	 * @return bool 
	 */
	private function _cacheExist()
	{
		$this->_versionFile = self::$wurflConfig->persistence['params']['dir'] . '/version.txt';
		return is_file($this->_versionFile);
	}
	
	/**
	 * Create the WURFL object and store the content. Use with precaution !!! As will load a lot of files
	 * @access public
	 * @return void
	 */
	public  function createWurflFactory()
	{
		if(!$this->_cacheExist())
		{
			$this->_buildFactory();
			$wurflManager = $this->_wurflManagerFactory->create(true);
			$info = $wurflManager->getWURFLInfo();
			$wurflInfo['version']     = $info->version;
			$this->_saveWurflVersion($wurflInfo['version']);
		}

	}
	
	/**
	 * Remove the content from the persistent storage 
	 * @access public
	 * @return void
	 */
	public function removeWurflFactory()
	{
		$this->_wurflManagerFactory->remove();
	}
	
	/**
	 * Get information about WURFL  previously saved in version.txt file
	 * @access public
	 * @return bidimensional array, with xmlFile and cache created date
	 */
	public function getWurflVersion()
	{
		if($this->_cacheExist())
		{
			$readFile = file($this->_versionFile);
			$result['xmlFileDate']    = $readFile['0'];
			$result['cacheDate']      = $readFile['1'];
		}
		else
		{
			$result['xmlFileDate']    = 'EMPTY CACHE';
			$result['cacheDate']      = 'EMPTY CACHE';
		}
		return $result;
	}
	
	/**
	 * Generate a special file with 2 tags:  build date, (YYYY-MM-DD, HH:ss) and xml file date (eg. 2011-04-24)
	 * @access private
	 * @return void
	 */
	private function _saveWurflVersion($xmlFileDate)
	{
		if(!$this->_cacheExist())
		{
			$writer = new Zend_Log_Writer_Stream($this->_versionFile);
			$formatter = new Zend_Log_Formatter_Simple('%message%' . PHP_EOL);
			$writer->setFormatter($formatter);
			$logger = new Zend_Log($writer);
			$logger->info($xmlFileDate);
			$logger->info(date("F j, Y, H:i"));   
		}
	}
	
	/**
	 * Return a huge un-usable object . Use it with precaution !
	 * @param object $server where is usually an object similar with $_SERVER global variable
	 * @access private , we don't want to be called within controller, is too expensive
	 * @return object
	 */
	private function _getDeviceForHttpRequest($server)
	{
		$wurflManager = $this->_wurflManagerFactory->create(true);
		return $wurflManager->getDeviceForHttpRequest($server);
	}
	
	/**
	 * Return a huge un-usable object . But only when cache exists. Use it with precaution !
	 * @param string UserAgent, usually $_SERVER["HTTP_USER_AGENT"]
	 * @access private , we don't want to be called within controller, is too expensive
	 * @return object
	 */
	private function _getDeviceForUserAgent($userAgent)
	{
		if($this->_cacheExist())
		{
			$wurflManager = $this->_wurflManagerFactory->create(true);
			return $wurflManager->getDeviceForUserAgent($userAgent);
		}
	}
	
	/** 
	 *  Return a nice usable array with device capabilities
	 * @access public 
	 * @param string $userAgent
	 * @return  array
	 */
	public function getDeviceCapabilities($userAgent)
	{
		if($this->_cacheExist())
		{
			return $this->_getDeviceForUserAgent($userAgent)->getAllCapabilities();
		}
	}
	
	/** 
	 * get from WURFL huge object only few interesting params
	 * @access public
	 * @param object $userAgent
	 * @return array with short summary related to device 
	 */		
	public function getDevice($userAgent)
	{
		// if cache is enabled in application.ini and APC is enabled 
		if($this->_cacheApc)
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
				// prepare the array and save it in cache 
			 	$device = ($this->_cacheExist()) ? $this->_prepareDeviceInfo($userAgent) : new StdClass();
				$this->_saveDeviceToCache($device, $cacheToken);
				return $device;
			}
				
		}	
		// No APC cache available
		else
		{
			$object = ($this->_cacheExist()) ? $this->_prepareDeviceInfo($userAgent) : new StdClass();
			return $object;
		}		
	}
	
	/**
	 * Core function. Prepare the device short info
	 * @access private
	 * @param object $userAgent
	 * @return object
	 */
	private function _prepareDeviceInfo($userAgent)
	{
		// prepare the object with device info
		$deviceCapabilities = $this->getDeviceCapabilities($userAgent);

		$device = new stdClass();
		$device->fallBack        = $this->getDeviceFallBack($userAgent);
		$device->brandName       = $deviceCapabilities['brand_name'];
		$device->modelName       = $deviceCapabilities['model_name'];
		$device->browserName     = $deviceCapabilities['mobile_browser'];
		$device->browserVersion  = $deviceCapabilities['mobile_browser_version']; 
		$device->deviceOs        = $deviceCapabilities['device_os'];
		$device->deviceOsVersion = $deviceCapabilities['device_os_version']; 
		$device->screenWidth     = $deviceCapabilities['resolution_width'];
		$device->screenHeight    = $deviceCapabilities['resolution_height'];
		$device->isTablet        = ($deviceCapabilities['is_tablet'] == 'true') ? TRUE : FALSE;
		$device->isMobile        = empty($deviceCapabilities['mobile_browser'])? FALSE : TRUE;
		$device->isSmartphone    = $this->_isSmartphone($deviceCapabilities, $device->isMobile, $device->deviceOs);
		$device->isIphone        = $this->_isIphone($device->fallBack);
		$device->isAndroid       = $this->_isAndroid($device->deviceOs);
		$device->isBlackberry    = $this->_isBlackberry($device->fallBack);
		$device->isSymbian       = $this->_isSymbian($device->deviceOs);
		$device->isWindowsMobile = $this->_isWindowsMobile($device->deviceOs, $device->isMobile);	
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
		if($this->_cacheApc->test($cacheToken))
		{
			$device = $this->_cacheApc->load($cacheToken);
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
		$this->_cacheApc->save($device, $cacheToken);
	}
	
	/**
	 * Check device capabilities for screen size. We assume that a screen size lower then 240x320 is not a real 
	 * smartphone, but more something that you can shove it up your ass ( a.k.a "feature phone")
	 * Second mandatory condition: to have an Operating System
	 * @param array $deviceCapabilities
	 * @param bool $isMobile
	 * @access private
	 * @return bool
	 */
	private function _isSmartphone($deviceCapabilities, $isMobile, $deviceOs) 
	{
		$isSmartphone =  FALSE;
		// if is not a mobile device , we will have surprises here :-)
		if($isMobile)
		{		
			$screenWidth  = $deviceCapabilities["resolution_width"];
			$screenHeight = $deviceCapabilities["resolution_height"];
			if($screenWidth >= 240  && $screenHeight >= 320 && !empty($deviceOs))
			{
				$isSmartphone =  TRUE;
			}
		}
		return $isSmartphone;
	}
	
	/**
	 * Check again device fallBack the string iphone
	 * @access private
	 * @return bool
	 */
	private function _isIphone($deviceFallBack)
	{
		$isIphone = FALSE;
		if(stripos($deviceFallBack, 'iphone') !== FALSE)
		{
			$isIphone = TRUE;	
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
		$isAndroid = FALSE;
		if(stripos($deviceOsName, 'Android') !== FALSE)
		{
			$isAndroid = TRUE;	
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
		$isBlackberry = FALSE;
		if(stripos($deviceFallBack, 'Blackberry') !== FALSE)
		{
			$isBlackberry = TRUE;	
		}
		return $isBlackberry;
	}

	/**
	 * Check if is a Symbian device
	 * @param object $deviceOsName
	 * @return bool
	 */
	private function _isSymbian($deviceOsName)
	{
		$isSymbian = FALSE;
		if(stripos($deviceOsName, 'Symbian') !== FALSE)
		{
			$isSymbian = TRUE;	
		}
		return $isSymbian;
	}	
	
	/**
	 * Check if is an Windows Mobile device 
	 * @param object $deviceOsName
	 * @param object $isMobile
	 * @return bool
	 */
	private function _isWindowsMobile($deviceOsName, $isMobile)
	{
		$isWindows = FALSE;
		if(stripos($deviceOsName, 'Windows') !== FALSE && $isMobile == TRUE)
		{
			$isWindows = TRUE;	
		}
		return $isWindows;
	}
	
	/**
	 * Prevent user to clone the instance 
	 * @return void
	 */
	final private function __clone()
	{
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	} 
}