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
 * Filter Phone Number
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotFilter
 * @author     DotKernel Team <team@dotkernel.com>
 */

class Dot_Filter
{	
	/**
	 * Filter options
	 * @access protected
	 * @var array
	 */
	protected $_options = array();
	/**
	 * Filtered data
	 * @var array
	 * @access private
	 */
	private $_data = array();
	/**
	 * Errors found on filter
	 * @var array
	 * @access private
	 */
	private $_error = array();
	/**
	 * Constructor
	 * @access public
	 * @param array $options [optional]
	 * @return Dot_Filter
	 */
	public function __construct($options = array())
	{		
		foreach ($options as $key =>$value)
		{
			$this->_options[$key] = $value;
		}
	}
	/**
	 * Filter data that was previously validated (input data)
	 * If data was not valid, filter the error message that will 
	 * be output to the user (output data) 
	 * @access public
	 * @return bool
	 */
	public function filter()
	{
		$this->_data = array();
		$this->_error = array();
		$values = $this->_options['values'];
		$validator = $this->_options['validator'];		
		$filter = new Zend_Filter();
        $filter->addFilter(new Zend_Filter_HtmlEntities());
        $filter->addFilter(new Zend_Filter_StringTrim());
		foreach ($values as $k=>$v)
		{
		    if($validator->isValid($values[$k]))
			{
				//filter the input     
				$this->_data[$k] = $filter->filter($values[$k]); 
			}
			else
			{
				foreach ($validator->getMessages() as $message)
				{
					//filter the output
					$this->_error[$k] = str_replace($values[$k], $filter->filter($values[$k]), $message);
				}
			}
		}		
		if(empty($this->_error))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	/**
	 * Get filtered data
	 * @access public
	 * @return array 
	 */
	public function getData()
	{
		return $this->_data;
	}	
	/**
	 * Get errors encounter on filtration
	 * @access public
	 * @return array
	 */
	public function getError()
	{
		return $this->_error;
	}
}