<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* General Statistics class. Interaction with Zend_Log and with application specific statistic actions
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/


class Dot_Statistic
{
	/**
	 * Constructor
	 * @access public
	 * @return object
	 */
	function __construct()
	{
		$this->db = Zend_Registry::get('database');
	}	
}