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
* Template engine, based on PHPLIB library
* @category   DotKernel
* @package    DotLibrary
* @subpackage DotTemplate
* @author     DotKernel Team <team@dotkernel.com>
*/


class Dot_Template_Cache extends Dot_Template
{
	/**
	 * Singleton instance
	 * @access protected
	 * @static
	 * @var Dot_Template
	 */
	protected static $_instance = null;	
	/**
	 * Singleton pattern implementation makes 'new' unavailable
	 * @access public
	 * @param string $root     Template root directory
	 * @param string $unknowns How to handle unknown variables
	 * @param array  $fallback Fallback paths
	 * @return void
	 */
	protected function __construct($root = '.', $unknowns = 'remove', $fallback='')
	{
		$this->setRoot($root);
		$this->setUnknowns($unknowns);
		if (is_array($fallback)) $this->fileFallbacks = $fallback;
	}
	/**
	 * Singleton pattern implementation makes 'clone' unavailable
	 * @access protected
	 * @return void
	 */
	protected function __clone()
	{}
	/**
	 * Returns an instance of Dot_View
	 * Singleton pattern implementation
	 * @access public
	 * @param string $root     Template root directory
	 * @param string $unknowns How to handle unknown variables
	 * @param array  $fallback Fallback paths
	 * @return Dot_Template
	 */
	public static function getInstance($root = '.', $unknowns = 'remove', $fallback='')
	{
		if (null === self::$_instance) {
			self::$_instance = parent::getInstance($root, $unknowns, $fallback);			
		}
		return self::$_instance;
	}	
	
}