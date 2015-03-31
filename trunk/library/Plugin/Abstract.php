<?php

/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    Frontend
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
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
		
		// uncomment the line below if you need the plugin config for all the plugins
		// $this->pluginConfiguration = Zend_Registry::get('pluginConfiguration');
		
		// plugin options (passed through constructor)
		$this->_options = $options;
	}

	/**
	 * Get plugin info
	 * @access public
	 * @return array $info
	 */
	abstract public function getPluginInfo();
} 