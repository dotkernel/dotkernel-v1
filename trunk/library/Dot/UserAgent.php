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
	 * Constructor, set the WURFL configuration paramethers
	 * @access public
	 * @return Dot_UserAgent
	*/
	function __construct()
	{		
		$this->config = Zend_Registry::get('configuration');
		require_once $this->config->resources->useragent->wurflapi->lib_dir . 'Application.php';
		// load WURFL configuration XML file
	  $wurflConfig = new WURFL_Configuration_XmlConfig($this->config->resources->useragent->wurflapi->config_file);
		// load the confiuration object in a class available variable 
	  $this->wurflConfig = $wurflConfig;
		
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
		#TODO what the "true" mean ?
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
		$info = $wurflManager->getWURFLInfo();
		$wurflInfo['version']     = $info->version;
		$wurflInfo['lastUpdated'] = $info->lastUpdated;

		return $wurflInfo; 
	}
	
	/**
	 * @param object $server where is usually an object similar with $_SERVER global variable
	 * @return object
	 * Most used method, and the api will create the WURFL_Request_GenericRequest instance 
	 */
	public function getDeviceForHttpRequest($server)
	{
		$wurflManager = $this->wurflManagerFactory->create(true);
		return $wurflManager->getDeviceForHttpRequest($server);
	}
	
	/**
	 * @param string UserAgent, usually $_SERVER["HTTP_USER_AGENT"]
	 * @return object
	 */
	public function getDeviceForUserAgent($userAgent)
	{
		$wurflManager = $this->wurflManagerFactory->create(true);
		return $wurflManager->getDeviceForUserAgent($userAgent);
	}
			
	
	#TODO write only once the data in Storage, and only from admin 
	#TODO data in Storage must be very summary, only strictly necessary informations, and presented as a Big Array
	#TODO data Storage in cache/wurfl folder , as 1 or 2 files, and moved when necessary in APC or memcached
	#TODO read-only  data by the other functions and controllers
	#TODO store in session 	only when is necessary, in Controller
	#TODO Dk articles about that strange experience 
	#TODO move here function getDevice from Dot_Kernel.php and everywhere where is called
	/*
	 * buildWurflPersistence()  // build the persistance for wurfl, saving the parsed wurf devices
	 */
}