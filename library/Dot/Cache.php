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
	 * @static
	 * @access private
	 * @return bool - if the cache can be used
	 */
	private static function _loadCache()
	{
		$configuration = Zend_Registry::get('configuration');
		if(!self::$_isLoaded)
		{
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
		return true;
	}
	
	/**
	 * Load the Cache 
	 * @static
	 * @access public
	 * @return bool - if the cache can be used
	 */
	public static function loadCache()
	{
		if(!self::$_isLoaded)
		{
			self::_loadCache();
		
			if(self::testCache())
			{
				return true;
			}
			return false;
		}
		
		return true;
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
	 * @param string $key
	 * @return string 
	 */
	public static function processKey($key)
	{
		// $message = 'The key must match the following RegEx pattern: [A-Za-z0-9_]*    More info here: http://www.dotkernel.com/dotkernel/caching-in-dotkernel-using-zend-framework/';
		// trigger_error($message, E_NOTICE);
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
	 * @static
	 * @access public
	 * @param string $testKey [optional] - cache key to write into
	 * @param mixed  $testValue [optional] - the value to compair
	 * @return boolean - if the cache works or not 
	 */
	public static function testCache($testKey = 'test', $testValue ='test')
	{
		if(!self::$_isLoaded)
		{
			return false;
		}
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
	 * @param stromg $module
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
			default: 
				return false;
		}
	}
}