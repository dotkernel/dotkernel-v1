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
 * Validate Phone Number
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotValidate
 * @author     DotKernel Team <team@dotkernel.com>
 */
 
class Dot_Validate_Phone extends Dot_Validate 
{
	/**
	 * Value of the phone to be validate
	 * @access private
	 * @var string
	 */
	private $_value = '';
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
	private $_options = array('code' => '',
							  'internationalPrefix' => 0,
							  'phoneLengthMin' => 0,
							  'phoneLengthMax' => 0,
							  'areaPositionStart' => 0,
							  'areaLength' => 0,
							  'prefixPositionStart' => 0,
							  'prefixLength' => 0
							  );
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
	 * Valid data after validation
	 * @var array
	 * @accesss private
	 */
	private $_data = array();
	/**
	 * Errors found on validation
	 * @var array
	 * @access private
	 */
	private $_error = array();
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
		if(is_array($options) && array_key_exists('values', $options))
		{
			$this->_value = $options['values'];
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
	 * @return bool
	 */
	public function isValid()
	{
		$dotFilterPhone = new Dot_Filter_Phone(array('value' => $this->_value));
		$this->phone = $dotFilterPhone->filter();

		$phoneLength = strlen($this->phone);
		if($this->_options['phoneLengthMin'] > $phoneLength || $phoneLength > $this->_options['phoneLengthMax'])
		{
			$this->_error = "'".$this->phone."'".' length is not between '.$this->_options['phoneLengthMin'].' and '.$this->_options['phoneLengthMax'].' characters like in '.$this->_countryCode.' country';
			return FALSE;
		}
		// internationalPrefix length is compared
		$intPrefixLength = strlen($this->_options['internationalPrefix']);
		if(substr($this->phone, 0, $intPrefixLength) != $this->_options['internationalPrefix'] && $phoneLength == $this->_options['phoneLengthMax'])
		{
			$this->_error =  "'".$this->phone."'".' length is not correct like in '.$this->_countryCode.' country. Tip: check the international prefix !';
			return FALSE;
		}
		$this->_area = substr($this->phone, $this->_options['areaPositionStart'] + $phoneLength - $this->_options['phoneLengthMin'], $this->_options['areaLength']);
		$this->_prefix = substr($this->phone, $this->_options['prefixPositionStart'] + $phoneLength - $this->_options['phoneLengthMin'], $this->_options['prefixLength']);		
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
			$this->_error =  "'".$this->phone."'".' area and prefix does not match for  like in '.$this->_countryCode.' country';
			return FALSE;
		}
	}	
	/**
	 * Get valid data
	 * @access public
	 * @return array 
	 */
	public function getData()
	{
		return $this->phone;
	}	
	/**
	 * Get errors encounter on validation
	 * @access public
	 * @return array
	 */
	public function getError()
	{
		return $this->_error;
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
