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
		$phoneArea = substr($phone, $this->_options['areaPositionStart'], $this->_options['areaLength']);
		$phonePrefix = substr($phone, $this->_options['prefixPositionStart'], $this->_options['prefixLength']);
		// internationalPrefix length is compared
		$intPrefixLength = strlen($this->_options['internationalPrefix']);
		if(substr($phone, 0, $intPrefixLength) != $this->_options['internationalPrefix'] && $phoneLength == $this->_options['phoneLengthMax'])
		{
			return FALSE;
		}
		$phoneArea = substr($phone, $this->_options['areaPositionStart']+$intPrefixLength, $this->_options['areaLength']);
		$phonePrefix = substr($phone, $this->_options['prefixPositionStart']+$intPrefixLength, $this->_options['prefixLength']);
		$conditionArea = '';
		$conditionPrefix = '';
		if(is_array($this->_options['allow']))
		{
			$allowKey = key($this->_options['allow']);
			$phoneAllowVar = 'phone'.ucfirst($allowKey);
			$conditionArea = ' && '.in_array($$phoneAllowVar, $this->_options['allow'][$allowKey]);
		}
		if(is_array($this->_options['deny']))
		{
			$denyKey = key($this->_options['deny']);
			$phoneDenyVar = 'phone'.ucfirst($denyKey);
			$conditionPrefix = !in_array($$phoneDenyVar, $this->_options['deny'][$denyKey]);
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
}
