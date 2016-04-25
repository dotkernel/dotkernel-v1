<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @copyright  Copyright (c) 2009-2016 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * Plugin_Base
 * 
 * @category   DotKernel
 * @package    Dot_Plugin
 * @author     DotKernel Team <team@dotkernel.com>
 */
abstract class Plugin_Abstract implements Plugin_Interface
{
	/**
	 * Plugin Options Array
	 * @var array - plugin options - provided by the Plugin_Loader 
	 */
	protected $_options;
	
	/**
	 * Plugin configuration found in xml file(s)
	 * @var unknown
	 */
	protected $_config = array() ;

	/**
	 * All constructs must be protected to prevent unwanted access 
	 * @access protected
	 */
	protected function __construct($options)
	{
		// main config might be needed
		$this->config = Zend_Registry::get('configuration');
		
		// db handler
		$this->db = Zend_Registry::get('database');
		
		// settings from db
		$this->settings = Zend_Registry::get('settings');
		
		// xml option - settings from configs/dots
		$this->xmlOption = Zend_Registry::get('option');
		
		// plugin options (passed through constructor)
		$this->_options = $options;
		
		$cacheKey = strtolower(get_class($this));

		// plugin config load from xml file(s)
		$config = Dot_Cache::load($cacheKey);
		if($config == false)
		{
			$config = $this->getPluginConfig();
			if($this->config->cache->cache_plugin_config)
			{
				Dot_Cache::save($config, $cacheKey);
			}
		}
		$this->_config = $config;
	}
	
	/**
	 * Get plugin from all files
	 * 
	 * Files are assigned key->value in the plugins.ini file
	 * 
	 * @access public
	 * @return array
	 */
	public function getPluginConfig()
	{
		$config = array();
		$files = $this->_options['config_file']; 
		foreach($files as $key => $configFile)
		{
			$config[$key] = $this->loadConfigFile($configFile, $key);
		}
		return $config;
	}
	
	/**
	 * Load Config from given file
	 *
	 * @access public
	 * @param string $filename
	 * @return array
	 */
	public function loadConfigFile($filename, $key='config')
	{
		$config = new Zend_Config_Xml($filename, APPLICATION_ENV);
		if($config instanceof Zend_Config_Xml)
		{
			return $config->toArray();
		}
		return array();
	}

	/**
	 * Get plugin info
	 * @access public
	 * @return array $info
	 */
	abstract public function getPluginInfo();
} 