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
	 * @param  array $phpSettings 
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
	 * @param  string $requestModule
	 * @return void
	 */
	public static function loadControllerFiles($requestModule)
	{
		$router = Zend_Registry::get('router');
		$modules = $router->controllers->toArray();
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
		// convert string to array 
		if(!is_array($modules))
		{
		   $modules = array($modules);
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
			if(file_exists(DOTKERNEL_PATH . '/' . $modulePath . 'views/' . $value . 'View.php'))
			{
				require_once(DOTKERNEL_PATH . '/' . $modulePath . 'views/' . $value . 'View.php');
			} 
			else die ('The file: ' . DOTKERNEL_PATH . '/' . $modulePath . 'views/' . $value . 'View.php' . ' does NOT exist');  
		}
	}
	
	/**
	 * Get the option variables from an xml file for the current dots
	 * 
	 * Used recursively, first take default.xml values. This values are 
	 * overwritten by the xml of the current dots
	 * 
	 * This method also stores the options in the cache, for faster access
	 * 
	 * @param string $requestModule
	 * @param string $requestController
	 * @return Zend_Config
	 */
	public static function getOptionVariables($requestModule,$requestController)
	{
		$option = array();
		
		// get the actual controller
		// fixes the  any_inexistent_controller caching
		// eg: localhost/DotKernel/module/inexistent_controller/
		$actualController = 'default';
		if($requestController == 'seo' || in_array($requestController, Dot_Route::getControllersForModule($requestModule)) )
		{
			$actualController = $requestController;
		}
		
		$cacheKey = 'option_' . $requestModule . '_' . $actualController ;
		$value = Dot_Cache::load($cacheKey);
		
		if($value != false)
		{
			$option = $value;
			return $option;
		}
		else 
		{
			if('default' == $requestController)
			{ 
				$dirOption = CONFIGURATION_PATH .'/';
				$fileOption = 'dots.xml';
			}
			else
			{			
				$dirOption = CONFIGURATION_PATH.'/dots/';
				$fileOption = strtolower($requestController).'.xml';
			}
			$validFile = new Zend_Validate_File_Exists();
			$validFile->setDirectory($dirOption);
			if($validFile->isValid($fileOption))
			{
				$xml = new Zend_Config_Xml($dirOption.$fileOption, 'dots');
				$arrayOption = $xml->variable->toArray();
				foreach ($arrayOption as $v)
				{
					if(in_array($v['option'], array('global', $requestModule)))
					{
						// first write global, then replace the values with the ones from $requestModule
						$option = array_replace_recursive($option,$v);
					}
				}
				
			}

			// overwritte the default options from dots.xml with the one of the current dots
			$option = new Zend_Config($option, true);
			if (Zend_Registry::isRegistered('option'))
			{
				$optionRegistered = Zend_Registry::get('option');
				$optionRegistered->merge($option);
				$value = Dot_Cache::save($optionRegistered, $cacheKey);
				return $optionRegistered;
			}
			$value = Dot_Cache::save($option, $cacheKey);
			return $option;
		}
	}
}