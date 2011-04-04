<?php
/**
 * WURFL API
 *
 * LICENSE
 *
 * This file is released under the GNU General Public License. Refer to the
 * COPYING file distributed with this package.
 *
 * Copyright (c) 2008-2009, WURFL-Pro S.r.l., Rome, Italy
 * 
 *  
 *
 * @category   WURFL
 * @package    WURFL_Cache
 * @copyright  WURFL-PRO SRL, Rome, Italy
 * @license
 * @version    $id$
 */
class WURFL_Cache_APCCacheProvider implements WURFL_Cache_CacheProvider {
	
	const EXTENSION_MODULE_NAME = "apc";
	
	private $expire;
	
	/**
	 *
	 * @param array $params
	 */
	public function __construct($params=null) {
		$this->_ensureModuleExistance();
		$this->expire = isset($params[WURFL_Cache_CacheProvider::EXPIRATION]) ? $params[WURFL_Cache_CacheProvider::EXPIRATION] : WURFL_Cache_CacheProvider::NEVER;
	}
	
	function get($key) {
		$value = apc_fetch($key);
		if($value === FALSE) {
			return NULL;
		}
		return $value;
	}

	function put($key, $value) {
		apc_store($key, $value, $this->expire);
	}
	
	function clear() {
		apc_clear_cache("user");
	}
	
 	/* 
 	 * Ensures the existance of the the PHP Extension apc
	 *
	 */
	private function _ensureModuleExistance() {
		if(!extension_loaded(self::EXTENSION_MODULE_NAME)) {
			throw new WURFL_Xml_PersistenceProvider_Exception("The PHP extension apc must be installed and loaded in order to use this cache provider");
		}
	}
}

