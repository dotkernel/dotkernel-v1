<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotLibrary
 * @copyright  Copyright (c) 2009-2012 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * Dot User Agent Wurfl Cloud integration
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotUserAgent
 * @see		  Dot_UserAgent
 * @author     DotKernel Team <team@dotkernel.com>
 * @since	  v1.6.0 2012-05-11 00:22:47
 */

class Dot_UserAgent_WurflCloud
{
	/**
	 * hold an instance of the class
	 * @var Dot_UserAgent_WurflCloud
	 * @static
	 */
	static $_instance;
	/**
	 * the wurfl cloud client
	 * @var WurflCloud_Client_Client
	 * @static
	 */
	private $_wurflCloudClient;
	/**
	 * the APC Cache
	 * @var Zend_Cache
	 * @static
	 */
	private $_cacheApc;
	/**
	 * contains last error when something went wrong per api call
	 * @var unknown_type
	 * @public
	 */
	public $lastError = '';

	/**
	 * private constructor, set the WURFL configuration paramethers
	 * @return void
	 */
	private function __construct()
	{
		$this->config = Zend_Registry::get('configuration');
		// load WURFL library and configuration XML file
		require_once $this->config->resources->useragent->wurflcloud->lib_dir . 'Client/Client.php';
		
		// if we have APC enabled, start the cache object too
		if(TRUE == $this->config->resources->useragent->wurflcloud->cache && function_exists('apc_cache_info') && (@apc_cache_info() !== FALSE) )
		{
			$frontendOptions = array('lifetime' => $this->config->resources->useragent->wurflcloud->cache_lifetime, 'automatic_serialization' => TRUE );
			$cache = Zend_Cache :: factory('Core', 'APC', $frontendOptions);
			$this->_cacheApc = $cache;
		}
		// Now is time to start the client
		$this->_buildClient();
	}
	
	/**
	 * The singleton method
	 * @return Dot_UserAgent_WurflCloud instance
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
	 * Get the version of the WURFL Cloud Client
	 * @return string
	 */
	public function getClientVersion()
	{
		return $this->_wurflCloudClient->getClientVersion();
	}
	/**
	 * Get the version of the WURFL Cloud Server. This is only available
	 * after a query has been made since it is returned in the response.
	 * @return string
	 */
	public function getAPIVersion()
	{
		return $this->_wurflCloudClient->getAPIVersion();
	}
	/**
	 * Returns the Cloud server that was used
	 * @return string
	 */
	public function getCloudServer()
	{
		return $this->_wurflCloudClient->getCloudServer();
	}
	
	/**
	 * Start the Wurfl Cloud library Clinet Manager
	 * @return void
	 */
	private function _buildClient()
	{
		// Create a WURFL Cloud Config
		$config = new WurflCloud_Client_Config();
		// Set your API Key here
		$config->api_key = $this->config->resources->useragent->wurflcloud->api_key;
		// Create a WURFL Cloud Client
		if(TRUE == $this->config->resources->useragent->wurflcloud->cache)
		{
			$wurflCloudClient = new WurflCloud_Client_Client($config, new WurflCloud_Cache_Cookie());
		}	
		else
		{
			$wurflCloudClient = new WurflCloud_Client_Client($config, new WurflCloud_Cache_Null());
		}
		$this->_wurflCloudClient = $wurflCloudClient;
	}
	
	/**
	 * Return UserAgent
	 * @param array $httpRequest
	 * @return string with user agent
	 */
	private function _getUserAgent($httpRequest = null)
	{
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		if(!empty($httpRequest))
		{
			$userAgent = $httpRequest['HTTP_USER_AGENT'];
		}
		return $userAgent;
	}
	
	/**
	 *  Return an array with device capabilities for your wurfl cloud account. This function is not using the cache
	 * @access public
	 * @param string $httpRequest
	 * @return  array
	 */
	public function getDeviceCapabilities($httpRequest = null)
	{
		try {
			$this->_wurflCloudClient->detectDevice($httpRequest);
			return $this->_wurflCloudClient->capabilities;
			$this->lastError = '';
		}
		catch(Exception $e) {
			$this->lastError = $e->getMessage();
		}
	}
	
	/**
	 * get response from WURFL Cloud and make some post processing
	 * @access public
	 * @param object $httpRequest
	 * @return array with short summary related to device
	 */
	public function getDevice($httpRequest = null)
	{
		// get user agent
		$userAgent = $this->_getUserAgent($httpRequest);
		// if cache is enabled in application.ini and APC is enabled
		if($this->_cacheApc)
		{
			$cacheToken = $this->config->resources->useragent->wurflcloud->cache_namespace . '_' . md5($userAgent);
			//check if we have the info there in cache
			$device = $this->_getDeviceFromCache($cacheToken);
			if($device)
			{
				return $device;
			}
			else
			{
				// prepare the array and save it in cache
				$device =  $this->_prepareDeviceInfo($httpRequest);
				$this->_saveDeviceToCache($device, $cacheToken);
				return $device;
			}
		
		}
		// No APC cache available
		else
		{
			$device = $this->_prepareDeviceInfo($httpRequest);
			return $device;
		}
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
	 * Core function. Prepare the device short info
	 * @access private
	 * @param string $httpRequest
	 * @return object
	 */
	private function _prepareDeviceInfo($httpRequest)
	{
		// prepare the object with device info
		$deviceCapabilities = $this->getDeviceCapabilities($httpRequest);
		
		$device = new stdClass();
		$device->deviceId        = $deviceCapabilities['id'];
		$device->browserName     = $deviceCapabilities['mobile_browser'];
		$device->deviceOs        = $deviceCapabilities['device_os'];
		$device->isMobile        = empty($deviceCapabilities['mobile_browser'])? FALSE : TRUE;
		$device->isSmartphone    = $this->_isSmartphone($device->isMobile, $device->deviceOs, $device->browserName);
		$device->isIphone        = $this->_isIphone($device->deviceId);
		$device->isAndroid       = $this->_isAndroid($device->deviceOs);
		$device->isBlackberry    = $this->_isBlackberry($device->deviceId);
		$device->isSymbian       = $this->_isSymbian($device->deviceOs);
		$device->isWindowsMobile = $this->_isWindowsMobile($device->deviceOs, $device->isMobile);
		
		return $device;
	}
	
	/**
	 * Check device capabilities
	 * Second mandatory condition: to have an Operating System
	 * @param array $deviceCapabilities
	 * @param bool $isMobile
	 * @access private
	 * @return bool
	 */
	private function _isSmartphone($isMobile, $deviceOs, $browserName)
	{
		$isSmartphone =  FALSE;
		if($isMobile && !empty($deviceOs) && !empty($browserName))
		{
			$isSmartphone =  TRUE;
		}
		return $isSmartphone;
	}
	
	/**
	 * Check again device id the string iphone
	 * @access private
	 * @return bool
	 */
	private function _isIphone($deviceId)
	{
		$isIphone = FALSE;
		if(stripos($deviceId, 'iphone') !== FALSE)
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
	 * Check again device id the string Blackberry
	 * @access private
	 * @return bool
	 */
	private function _isBlackberry($deviceId)
	{
		$isBlackberry = FALSE;
		if(stripos($deviceId, 'Blackberry') !== FALSE)
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