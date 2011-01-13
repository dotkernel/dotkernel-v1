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
* Validate different inputs
* @category   DotKernel
* @package    DotLibrary
* @subpackage DotValidate
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Validate
{	
	/**
	 * Validate options
	 * @access private
	 * @var array
	 */
	private $_options = array();
	/**
	 * Constructor
	 * @access public
	 * @param array $options [optional]
	 * @return Dot_Validate
	 */
	public function __construct($options = array())
	{		
		$this->_options = $options;
	}
}
