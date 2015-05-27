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
		self::$_cache = Zend_Cache::factory('Core', $backendName, $frontendOptions, $backendOptions);
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
	public static function save($data, $key, $lifetime = false, $tags = array() )
	{
		if(self::$_isLoaded)
		{
			self::$_cache->save($data, self::processKey($key), $tags, $lifetime);
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
			return self::$_cache->load(self::processKey($key), $doNotTestCacheValidity = false, $doNotUnserialize = false);
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
	 * Return an array of stored cache keys
	 *
	 * @return array array of stored cache keys (string)
	 */
	public static function getKeys()
	{
		if(! self::$_isLoaded)
		{
			return false;
		}
		return self::$_cache->getIds();
	}

	/**
	 * Return the filling percentage of the backend storage
	 *
	 * @see Zend_Cache_Core
	 * @see http://framework.zend.com/manual/1.12/en/zend.cache.frontends.html#zend.cache.frontends.core
	 *
	 * @static
	 * @return int integer between 0 and 100
	 */
	public static function getFillingPercentage()
	{
		if(!self::$_isLoaded)
		{
			return false;
		}
		return self::$_cache->getFillingPercentage();
	}

	/**
	 * Return an array of metadatas for the given cache id
	 *
	 * The array will include these keys :
	 * - expire : the expire timestamp
	 * - tags : a string array of tags
	 * - mtime : timestamp of last modification time
	 *
	 * @see Zend_Cache_Core
	 * @see http://framework.zend.com/manual/1.12/en/zend.cache.frontends.html#zend.cache.frontends.core
	 * 
	 * @static
	 * @param string $cacheKey cache key
	 * @return array array of metadatas (false if the cache id is not found)
	 */
	public static function getMetadatas($cacheKey)
	{
		if(!self::$_isLoaded)
		{
			return false;
		}
		return self::$_cache->getMetadatas($cacheKey);
	}

	/**
	 * Give (if possible) an extra lifetime to the given cache id
	 *
	 * @static
	 * @param string $id cache id
	 * @param int $extraLifetime
	 * @return boolean true if ok
	 */
	public static function touch($id, $extraLifetime)
	{
		if(!self::$_isLoaded)
		{
			return false;
		}
		return self::$_cache->touch($id, $extraLifetime);
	}
	
	
	/**
	 * Return an array of stored cache Keys which match given tags
	 *
	 * In case of multiple tags, a logical AND is made between tags
	 *
	 * @param array $tags array of tags
	 * @return array array of matching cache Keys (string)
	 */
	public static function getKeysMatchingTags($tags = array())
	{
		if(!self::$_isLoaded)
		{
			return false;
		}
		return self::$_cache->getIdsMatchingTags($tags);
	}
	
	/**
	 * Return an array of stored cache Keys which don't match given tags
	 *
	 * In case of multiple tags, a logical OR is made between tags
	 *
	 * @see Zend_Cache_Core
	 * @see http://framework.zend.com/manual/1.12/en/zend.cache.frontends.html#zend.cache.frontends.core
	 * 
	 * @static
	 * @param array $tags array of tags
	 * @return array array of not matching cache Keys (string)
	 */
	public static function getKeysNotMatchingTags($tags = array())
	{
		if(!self::$_isLoaded)
		{
			return false;
		}
		return self::$_cache->getIdsNotMatchingTags($tags);
	}
	
	/**
	 * Return an array of stored tags
	 *
	 * @see Zend_Cache_Core
	 * @see http://framework.zend.com/manual/1.12/en/zend.cache.frontends.html#zend.cache.frontends.core
	 * 
	 * @static
	 * @return array array of stored tags (string)
	 */
	public static function getTags()
	{
		if(! self::$_isLoaded)
		{
			return false;
		}
		return self::$_cache->getTags();
	}
	
	
	/**
	 * Clean cache entries
	 *
	 * Available modes are :
	 * 'all' (default)  => remove all cache entries ($tags is not used)
	 * 'old'            => remove too old cache entries ($tags is not used)
	 * 'matchingTag'    => remove cache entries matching all given tags
	 *                     ($tags can be an array of strings or a single string)
	 * 'notMatchingTag' => remove cache entries not matching one of the given tags
	 *                     ($tags can be an array of strings or a single string)
	 * 'matchingAnyTag' => remove cache entries matching any given tags
	 *                     ($tags can be an array of strings or a single string)
	 *
	 * @see Zend_Cache_Core
	 * @see http://framework.zend.com/manual/1.12/en/zend.cache.frontends.html#zend.cache.frontends.core
	 *
	 * @static
	 * @param  string       $mode
	 * @param  array|string $tags
	 * @throws Zend_Cache_Exception
	 * @return boolean True if ok
	 */
	public static function clean($mode = 'all', $tags = array())
	{
		if(!self::$_isLoaded)
		{
			return false;
		}
		return self::$_cache->clean($mode, $tags);
	}
	
	/**
	 * Returns ALL Cache details in one array
	 * 
	 * Array contains:
	 *   KEY               DESCRIPTION
	 *   isLoaded          bool   - if cache is loaded
	 *   isSupportingTags  bool   - if cache backend/provider supports tags 
	 *   keyCount          int    - stored keys count 
	 *   keys              array  - 2-level Array with all stored keys in cache and their metadata
	 *   importantKeys     array  - 2-level Array with important keys in cache and their metadata, theese keys can be added in application.ini
	 *   tagsCount         int    - stored tags count - 0 if tags not supported
	 *   tags              array  - 2-level Array with all stored tags, and keys within each tag - empty if tags not supported
	 *   fill              int    - Integer representing the cache filling percent 
	 *   lifetime          int    - Integer representing the GLOBAL cache lifetime settings (lifetime can be set individually on keys)
	 *   cache_id_prefix   string - The prefix (or namespace) to save in eg. dotkernel_
	 */
	public static function getCacheInfo()
	{
		$info = array('isLoaded' => false, 'isSupportingTags' => false, 'keyCount' => 0, 'tags' => array(), 'fill' => 0, 'lifetime'=>0);
		if(!self::$_isLoaded)
		{
			return $info;
		}
		// the cache is loaded if we reached this point
		$info['isLoaded'] = true;

		// keys 
		$keys = self::getKeys();
		$info['keyCount'] = count($keys);
		foreach($keys as $key)
		{
			$info['keys'][$key] = self::getMetadatas($key);
		}
		
		// test tags capability
		$info['isSupportingTags'] = self::testTags();
		
		// parse tags info 
		$tags = array();
		if($info['isSupportingTags'])
		{
			$tags = self::getTags();
		}
		$info['tags'] = array();
		
		foreach($tags as $tag)
		{
			$info['tags'][$tag] = self::getKeysMatchingTags(array($tag));
		}
		
		$info['fill'] = self::getFillingPercentage();
		
		$configuration = Zend_Registry::get('configuration');
		$info['lifetime'] = $configuration->cache->lifetime;
		$info['cache_id_prefix'] = $configuration->cache->namespace.'_';
		$info['importantKeys'] = self::_getImportantCacheKeys($info['keys']);
		return $info;
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
	public static function testCache($testKey = 'test', $testValue = 'test')
	{
		if(!self::$_isLoaded)
		{
			return false;
		}
		// only process the key once, use it twice
		$testKey = self::processKey($testKey);
	
		self::save($testValue, $testKey);
		if(self::load($testKey) == $testValue)
		{
			self::remove($testKey);
			return true;
		}
		return false;
	}
	
	/**
	 * Test the tags capability in cache
	 * 
	 * This will test:
	 *  If the tag defined will exist in tag list 
	 *  If the cache key matches with the id's in given tag
	 *  If the value of cache key is the same with the one given
	 * 
	 * This test should be executed only once
	 * 
	 * @param string $testKey [optional]
	 * @param string $testValue [optional]
	 * @param string $testTag [optional]
	 * @return boolean
	 */
	
	public static function testTags($testKey = 'test', $testValue = 'test', $testTag = 'test')
	{
		if(! self::$_isLoaded)
		{
			return false;
		}
		$testKey = self::processKey($testKey);
		$testTag = array(self::processKey($testTag));
		try
		{
			self::$_cache->save($testValue, $testKey, $testTag);
			if( in_array($testTag[0], self::getTags())
				&& in_array($testKey, self::getKeysMatchingTags())
				&& self::load($testKey) == $testValue )
			{
				self::remove($testKey);
				return true;
			}
		}
		catch(Exception $e)
		{
			return false;
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
	
	/**
	 * Get Important Cache Keys
	 *
	 * Selects the main cache keys from given cache key list
	 * Useful when you want to change an xml or plugins.ini
	 * 
	 * Returns empty array if cache is not loaded, or if no cache keys loaded
	 *
	 * @static 
	 * @access private
	 * @param array
	 * @return array $importantKeys
	 */
	private static function _getImportantCacheKeys($cacheKeys = array())
	{
		if(empty($cacheKeys) || !self::$_isLoaded)
		{
			return array();
		}
		$globalKeys = array('acl_role', 'router');
		$importantKeys = array();
		$routerArray = Zend_Registry::get('router')->toArray();
		$customKeyList = self::_getCustomKeyList();
		$keysToCheck = array();
		foreach($routerArray['controllers'] as $module => $controllerList)
		{
			// if there is only one element treat as array, not as string
			if(is_string($controllerList))
			{
				$controllerList = array($routerArray['controllers'][$module]);
			}
			array_push($controllerList, 'seo', 'default');
			foreach($controllerList as $controller)
			{
				$keysToCheck[] = 'option_'.strtolower($module.'_'.$controller);
			}
		}
		
		$keysToCheck = array_merge($keysToCheck, $customKeyList, $globalKeys);

		foreach($keysToCheck as $cacheKey)
		{
			if(isset($cacheKeys[$cacheKey]))
			{
				$importantKeys[$cacheKey] = $cacheKeys[$cacheKey];
			}
		}
		return $importantKeys;
	}
	
	/**
	 * Get Custom Important Keys
	 * 
	 * Gets the cache keys from application.ini
	 * 
	 * @static 
	 * @access private
	 * @param array
	 * @return array $importantKeys
	 */
	private static function _getCustomKeyList()
	{
		$customKeyList = Zend_Registry::get('configuration')->cache->important_key_list;
		if(null == $customKeyList)
		{
			return array();
		}
		return $customKeyList->toArray();
	}
}