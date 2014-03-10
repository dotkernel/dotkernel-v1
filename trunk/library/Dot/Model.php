<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Will be extended by all models
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Model
{
	/**
	 * Dot_Model constructor
	 * @access public
	 * @return Dot_Model
	 */
	public function __construct()
	{
		$this->db        = Zend_Registry::get('database');
		$this->config    = Zend_Registry::get('configuration');
		$this->settings  = Zend_Registry::get('settings');
		$this->option    = Zend_Registry::get('option');
	}
}