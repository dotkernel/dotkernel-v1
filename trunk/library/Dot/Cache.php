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
 * Cache handler for DotKernel
 * @category   DotKernel
 * @package    DotLibrary
 * @author     DotKernel Team <team@dotkernel.com>
 */

class Dot_Cache
{
	private static $_isLoaded = false ;
	private static $_cache = null;
	
	/**
	 * Load the Cache with the parameters found in application.ini
	 * 
	 * Returns true if the cache can be used, false on fail
	 * 
	 * @static
	 * @access private
	 * @return bool
	 */
	private static function _loadCache()
	{
		$configuration = Zend_Registry::get('configuration');
		// only disable automatic serialization if you know what you're doing
		$frontendOptions = array(
			'lifetime' => $configuration->cache->lifetime,
			'caching' => $configuration->cache->enable,
			'cache_id_prefix' => $configuration->cache->namespace.'_',
			'automatic_serialization' => true 
		);

		// making sure it's lowercase
		$backendName = strtolower($configuration->cache->factory);
		$backendOptions = array();
		if(!self::_isExtensionLoaded($backendName))
		{
			self::$_isLoaded = false;
			return false;
		}
		//
		if(null !== $configuration->cache->$backendName)
		{
			foreach($configuration->cache->$backendName as $key => $value)
			{
				$backendOptions[$key] = $value;
			}
		}
		self::$_cache = $cache = Zend_Cache::factory('Core', $backendName, $frontendOptions, $backendOptions);
		self::$_isLoaded = true;
		return true;
	}
	
	/**
	 * Load the Cache 
	 * @static
	 * @access public
	 * @return bool
	 */
	public static function loadCache()
	{
		if(!self::$_isLoaded)
		{
			self::_loadCache();
		}
		if(self::testCache())
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Save to cache
	 * @param mixed $data
	 * @param string $key
	 * @return boolean
	 */
	public static function save($data, $key)
	{
		if(self::$_isLoaded)
		{
			self::$_cache->save($data, self::processKey($key));
			return true;
		}
		return false;
	}
	
	/**
	 * Save to cache
	 * @param string $key
	 * @return mixed
	 */
	public static function load($key)
	{
		if(self::$_isLoaded)
		{
			return self::$_cache->load(self::processKey($key));
		}
		return false;
	}
	
	/**
	 * Process the key, triggering a notice is optional
	 * 
	 * The use of this method is to ensure that 
	 * load, save and delete methods processes keys in the same way
	 * 
	 * @param string $key
	 * @return string 
	 */
	public static function processKey($key)
	{
		/*
		$message = 'The key must match the following RegEx pattern: [A-Za-z0-9_]*   
		  More info here: http://www.dotkernel.com/dotkernel/caching-in-dotkernel-using-zend-framework/';
		 trigger_error($message, E_NOTICE);
		 //*/
		return strtolower(preg_replace("/[^A-Za-z0-9 ]/", '_', $key));
	}
	
	/**
	 * Remove from cache
	 * @param string $key
	 * @return bool - if the deletion was succesful
	 */
	public static function remove($key)
	{
		if(!self::$_isLoaded)
		{
			return false;
		}
		self::$_cache->remove(self::processKey($key));
		return true;
	}
	
	/**
	 * Test Cache
	 * 
	 * Returns true if the cache works
	 * Returns false in other cases (cache isn't loaded, cache doesn't work correctly)
	 * 
	 * @static
	 * @access public
	 * @param string $testKey [optional] - cache key to write into
	 * @param mixed  $testValue [optional] - the value to compair
	 * @return boolean
	 */
	public static function testCache($testKey = 'test', $testValue ='test')
	{
		if(!self::$_isLoaded)
		{
			return false;
		}
		// only process the key once, use it twice
		$testKey =  self::processKey($testKey);
		
		self::$_cache->save($testValue,$testKey);
		if(self::$_cache->load($testKey) == $testValue)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Check if needed extension is existing
	 * 
	 * Other caching extensions may be added
	 * Make sure they have a Zend_Cache Backend
	 * 
	 * @param string $module
	 * @return boolean
	 */
	private static function _isExtensionLoaded($module)
	{
		switch($module)
		{
			case 'file':
				return true;
			case 'apc': 
				return extension_loaded('apc');
			// here you will add other caching extensions
			// @see Zend_Cache Backends
			default: 
				return false;
		}
	}
}