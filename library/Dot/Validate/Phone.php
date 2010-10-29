<?php 
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotLibrary
 * @copyright  Copyright (c) 2009-2010 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */
 
 /**
 * Validate Phone Number
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotValidate
 * @author     DotKernel Team <team@dotkernel.com>
 */
 
class Dot_Validate_Phone extends Dot_Validate {
	/**
	 * Country code for which the phone should be validate
	 * @access private
	 * @var string
	 */
	private $_countryCode = 'US';
	/**
	 * Phone options
	 * @access private
	 * @var array
	 */
	private $_options = array();
	/**
	 * Phone area
	 * @access private
	 * @var string
	 */
	private $_area = '';
	/**
	 * Phone prefix
	 * @access private
	 * @var string
	 */
	private $_prefix = '';
	/**
	 * Construct that receive as parameters the phone number and some options as array
	 * @access public
	 * @param array $options [optional]
	 * @return Dot_Validate_Phone
	 */
	public function __construct($options = array())
	{		
		if(is_array($options) && array_key_exists('countryCode', $options))
		{
			$this->_countryCode = $options['countryCode'];
		}
		
		$xml = new Zend_Config_Xml(CONFIGURATION_PATH.'/phone.xml');
		$phoneOptions = $xml->numbers->country->toArray();
		foreach ($phoneOptions as $key => $val)
		{
			if($val['code'] == $this->_countryCode)
			{
				$this->_options = $val;
				break;
			}
		}	
	}
	/**
	 * Validate phone number by country code
	 * @access public
	 * @param string $phone
	 * @return bool
	 */
	public function isValid($phone)
	{
		$phoneLength = strlen($phone);
		if($this->_options['phoneLengthMin'] > $phoneLength || $phoneLength > $this->_options['phoneLengthMax'])
		{
			return FALSE;
		}
		// internationalPrefix length is compared
		$intPrefixLength = strlen($this->_options['internationalPrefix']);
		if(substr($phone, 0, $intPrefixLength) != $this->_options['internationalPrefix'] && $phoneLength == $this->_options['phoneLengthMax'])
		{
			return FALSE;
		}
		$this->_area = substr($phone, $this->_options['areaPositionStart'] + $phoneLength - $this->_options['phoneLengthMin'], $this->_options['areaLength']);
		$this->_prefix = substr($phone, $this->_options['prefixPositionStart'] + $phoneLength - $this->_options['phoneLengthMin'], $this->_options['prefixLength']);		
		$conditionArea = '';
		$conditionPrefix = '';
		if(is_array($this->_options['allow']))
		{
			$allowKey = key($this->_options['allow']);
			$phoneAllowVar = '_'.$allowKey; //  "_area" used below as $this->_area
			$conditionArea = ' && '.in_array($this->$phoneAllowVar, $this->_options['allow'][$allowKey]);
		}
		if(is_array($this->_options['deny']))
		{
			$denyKey = key($this->_options['deny']);
			$phoneDenyVar = '_'.$denyKey; //  "_prefix" used below as $this->_prefix
			$conditionPrefix = !in_array($this->$phoneDenyVar, $this->_options['deny'][$denyKey]);
		}
		if($conditionArea && $conditionPrefix)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	/**
	 * Return phone area. 
	 * If no param set when calling this method, get phone area from isValid() method
	 * @param string $phone [optional]
	 * @return string
	 */
	public function getArea($phone = '')
	{
		if($phone != '')
		{
			$phoneLength = strlen($phone);
			$this->_area = substr($phone, $this->_options['areaPositionStart'] + $phoneLength - $this->_options['phoneLengthMin'], $this->_options['areaLength']);
		}
		return $this->_area;
		
	}
	/**
	 * Return phone prefix. 
	 * If no param set when calling this method, get phone prefix from isValid() method
	 * @param string $phone [optional]
	 * @return string
	 */
	public function getPrefix($phone = '')
	{
		if($phone != '')
		{
			$phoneLength = strlen($phone);
			$this->_prefix = substr($phone, $this->_options['prefixPositionStart'] + $phoneLength - $this->_options['phoneLengthMin'], $this->_options['prefixLength']);	
		}
		return $this->_prefix;		
	}
}
