<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * Plugin_Loader
 * 
 * @category   DotKernel
 * @package    Dot_Plugin
 * @author     DotKernel Team <team@dotkernel.com>
 */
 class Plugin_Loader
{
	private $_plugins = array();
	private $_pluginConfiguration; 
	private static $_instance;
	
	/**
	 * Loader Init
	 * @access public
	 */
	private function __construct()
	{
		$this->_pluginConfiguration = Zend_Registry::get('pluginConfiguration');
	}
	
	/**
	 * Singleton implementation 
	 * @return Plugin_Loader
	 */
	public static function getInstance()
	{
		if(!self::$_instance instanceof self)
		{
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	/**
	 * Is Plugin Enabled
	 * @access public 
	 * @param string $vendor
	 * @param string $pluginName
	 * @return bool $isEnabled
	 */
	public function isPluginEnabled($vendor, $pluginName)
	{
		// don't even look for it if it's not enabled from plugins.ini 
		if( isset( $this->_pluginConfiguration->plugin->$vendor->$pluginName->enable))
		{
			if(true != $this->_pluginConfiguration->plugin->$vendor->$pluginName->enable) 
			{
				return false;
			}
			
			// checking if it exists as a class 
			if(!$this->pluginExists($vendor, $pluginName))
			{
				return false;
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Check if the plugin is installed
	 * @access private 
	 * @param string $vendor
	 * @param string $pluginName
	 * @return bool $isEnabled
	 */
	public function pluginExists($vendor, $pluginName)
	{
		return class_exists('Plugin_'.$vendor.'_'.$pluginName);
	}
	
	/**
	 * Get Plugin Options
	 * @param unknown $vendor
	 * @param unknown $pluginName
	 * @return multitype:
	 */
	private function _getPluginOptions($vendor, $pluginName)
	{
		return $this->_pluginConfiguration->plugin->$vendor->$pluginName->toArray();
	}
	
	/**
	 * Load plugin if available
	 * 
	 * Loads the plugin if the availability check succeeds, 
	 * it automatically provides the plugin settings 
	 * The loader returns false if plugin failed loading
	 * 
	 * @access public
	 * @param string $vendor
	 * @param string $pluginName
	 * @return Plugin_Interface
	 */
	public function loadPlugin($vendor, $pluginName)
	{
		// check if we already loaded the plugin 
		// if you want more instances of the same plugin
		// just remove the if block below
		if(isset($this->_plugins[$vendor][$pluginName]))
		{
			return $this->_plugins[$vendor][$pluginName];
		}
		
		if($this->isPluginEnabled($vendor, $pluginName))
		{
			$pluginClass = 'Plugin_'.$vendor.'_'.$pluginName;
			
			$options = $this->_getPluginOptions($vendor, $pluginName);
			$plugin = $pluginClass::load($options);
			if($plugin != false)
			{
				$this->_plugins[$vendor][$pluginName] = $plugin;
			}
			return $plugin;
		}
		return false;
	}
	
	/**
	 * Gets all plugins with Enable/Disable Status
	 * @access public
	 * @return array
	 */
	public function getAllPlugins()
	{
		$i = 0 ;
		$plugins = array();
		$allPluginSettings = $this->_pluginConfiguration->plugin;
		if( ! $allPluginSettings)
		{
			$allPluginSettings = array();
		}
		else
		{
			$allPluginSettings = $allPluginSettings->toArray();
		}
		
		foreach($allPluginSettings as $vendor => $pluginList)
		{
			foreach($pluginList as $pluginName => $pluginSettings)
			{
				$plugins[$i]['vendor'] = $vendor; 
				$plugins[$i]['pluginName'] = $pluginName;
				$plugins[$i]['enabled'] = (bool)$pluginSettings['enable'];
				$i++;
			}
		}
		return $plugins;
	}
 }