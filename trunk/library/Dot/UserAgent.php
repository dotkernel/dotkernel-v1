<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
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
	 * Config Array
	 * @var array $config
	 * @access private
	 */
	private $_config;

	/**
	 * User Agent Detector
	 * 
	 * And a good illustration of static class members 
	 * 
	 * @var Dot_UserAgent_Base
	 * @access private 
	 * @static
	 */
	private static $_detector;
	
	/**
	 * Constructor,
	 * @access public
	 * @return Dot_UserAgent
	*/
	public function __construct()
	{
		$this->_config = Zend_Registry::get('configuration');
	}

	/**
	 * Get HTTP Request Device Info from UA Detector
	 * Device may be Bot, Checker, Console, Desktop, Email, Feed, Mobile, Offline, Probe, Spam, Text, Validator
	 * @return object
	 */
	public function getDeviceInfo($httpRequest)
	{
		if(self::$_detector === null)
		{
			return array();
		}
		self::$_detector->getDeviceInfo($httpRequest);
	}
	
	/**
	 * 
	 */
	public function createDetector($config = null, $detectorName = 'Null')
	{
		if($detectorName == 'Null')
		{
			return new Dot_UserAgent_Null(null);
		}
		$className = 'Dot_UserAgent_'.$detectorName;
		if(class_exists($className))
		{
			self::$_detector = new $className($config);
		}
	}
	
	/**
	 * Check the Detector Status
	 * Tests if the Detector package is installed and working
	 * @return boolean
	 */
	public function checkApi()
	{
		if(self::$_detector === null)
		{
			return false;
		}
		return self::$_detector->checkApi();
	}

}