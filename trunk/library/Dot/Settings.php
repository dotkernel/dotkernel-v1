<?php
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Loading Settings from database, also set PHP settings from config file 
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Settings
{
	/**
	 * Constructor
	 * @access public
	 * @return Dot_Settings
	 */
	public function __construct ()
	{
	}
	/**
	 * Get settings from database, table, and load into registry $settings
	 * @access public
	 * @static
	 * @param  database connection singleton
	 * @return object with values from setting table
 	 */
	public static function getSettings()
	{
		$settings = array();
		$db = Zend_Registry::get('database');
		$select = $db->select()
					 ->from('setting');
		$results = $db->fetchAll($select);
		foreach ($results as $key => $val)
		{
			$settings[$val['key']] = $val['value'];
		}	
		return (object)$settings;
	}
	/**
	 * Set PHP configuration settings
	 * @access public 
	 * @static
	 * @param  array $settings 
	 * @param  string $prefix Key prefix to prepend to array values (used to map . separated INI values)
	 * @return copied from Zend_Application class
 	 */
	public static function setPhpSettings(array $phpSettings, $prefix = '')
	{
		foreach ($phpSettings as $key => $value)
		{
			$key = empty($prefix) ? $key : $prefix . $key;
			if (is_scalar($value)) ini_set($key, $value);
			elseif (is_array($value))  self::setPhpSettings($value, $key . '.');
		}		
	} 
	/**
	 * Require the files according to MVC pattern, and the modules there are in application.ini file
	 * @access public
	 * @static
	 * @param  $requestModule
	 * @return void
	 */
	public static function loadControllerFiles($requestModule)
	{
		$resource = Zend_Registry::get('resource');
		$modules = $resource->controllers->toArray();		
		
		/**
		 *  if we are in frontend , we have an empty variable for $requestModule
		 *  Also, fix with $modulePath for modules path other then frontend
		 */		
		if( $requestModule != '' )
		{
			$modulePath = $requestModule . '/';
		}
		else
		{
			$modulePath = '';
			$requestModule = 'frontend';
		}
		// get the list of controllers for that specific module
		if(array_key_exists($requestModule, $modules))
		{
			$modules = $modules[$requestModule];
		}
		else 
		{
			die ('You must define at least one controller for the <b>' . $requestModule . '</b> module');
		}
		// Now require the files specific for each controller
		foreach ($modules as $value) 
		{
			// MODEL class
			if(file_exists(DOTKERNEL_PATH . '/' . $modulePath . $value . '.php'))
			{
				require_once(DOTKERNEL_PATH . '/' . $modulePath . $value . '.php');
			} 
			else die ('The file: ' . DOTKERNEL_PATH . '/' . $modulePath . $value . '.php' . ' does NOT exist');  
			// VIEW class
			if(file_exists(DOTKERNEL_PATH . '/' . $modulePath . 'views/' . ucfirst($value) . 'View.php'))
			{
				require_once(DOTKERNEL_PATH . '/' . $modulePath . 'views/' . ucfirst($value) . 'View.php');		
			} 
			else die ('The file: ' . DOTKERNEL_PATH . '/' . $modulePath . 'views/' . ucfirst($value) . 'View.php' . ' does NOT exist');  
		}
	}
	/**
	 * Get the scope variables from an xml file for the current dots
	 * @param string $requestModule
	 * @param string $requestController
	 * @return Zend_Config
	 */
	public static function getScopeVariables($requestModule,$requestController)
	{		
		$scope = array();	
		$dirScope = CONFIGURATION_PATH.'/dots/';
		$fileScope = strtolower($requestController).'.xml';
		$validFile = new Zend_Validate_File_Exists();
		$validFile->setDirectory($dirScope);		
		if($validFile->isValid($fileScope))
		{
			$xml = new Zend_Config_Xml($dirScope.$fileScope, 'body');
			$arrayScope = $xml->variable->toArray();
			foreach ($arrayScope as $v)
			{
				if(in_array($v['scope'], array('global', $requestModule)))
				{				
					$scope = array_merge_recursive($scope,$v);
				}
			}
		}
		$scope = new Zend_Config($scope);
		return $scope;
		
	} 

}