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
 * Filter Phone Number
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotFilter
 * @see		   Dot_Filter
 * @author     DotKernel Team <team@dotkernel.com>
 */

class Dot_Filter_Phone extends Dot_Filter
{
	/**
	 * Filter options
	 * @access protected
	 * @var array
	 */
	protected $_options = array();
	/**
	 * Valid data after validation
	 * @var array
	 * @access private
	 */
	private $_data = array();
	/**
	 * Errors found on validation
	 * @var array
	 * @access private
	 */
	private $_error = array();
	/**
	 * Constructor
	 * @access public
	 * @param array $options [optional]
	 * @return Dot_Filter_Phone
	 */
	public function __construct($options = array())
	{
		$this->_options = $options;
	}
	/**
	 * Filter phone number, only digits will pass
	 * @access public
	 * @return string
	 */
	public function filter()
	{
		// only pass the digits characters from a phone number
		$filter = new Zend_Filter_Digits();
		return $filter->filter($this->_options['value']);
	}
}