<?php
/**
 * DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id: Email.php 872 2015-01-05 16:34:50Z gabi $
*/

/**
 * DotKernel RSS Reader Model
* @category   DotKernel
* @package    DotPlugin
* @subpackage RSS_Reader 
* @author     DotKernel Team <team@dotkernel.com>
*/

class Plugin_DotKernel_RssReader extends Plugin_Abstract
{
	
	const PLUGIN_VENDOR = 'DotKernel';
	const PLUGIN_NAME = 'RssReader';
	const PLUGIN_VERSION = '1.0.0';
	
	/**
	 * Get plugin info
	 * @access public
	 * @return array $info
	 */
	public function getPluginInfo()
	{
		$info = array(
			'vendor'=>self::PLUGIN_VENDOR ,
			'name'=>self::PLUGIN_NAME,
			'version'=>self::PLUGIN_VERSION
		);
		return $info;
	}
	
	/**
	 * Load plugin instance with given settings
	 * @static
	 * @return object $plugin - the plugin handler
	 */
	public static function load($options)
	{
		if(1 == $options['enable'])
		{
			return new self($options);
		}
		return false;
	}
	
	public static function getFeed(){
		;
	}
}