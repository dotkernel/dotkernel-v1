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
	private $countryCode = 'US';
	/**
	 * Phone options
	 * @access private
	 * @var array
	 */
	private $options = array();
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
			$this->countryCode = $options['countryCode'];
		}
		
		$xml = new Zend_Config_Xml(CONFIGURATION_PATH.'/phone.xml');
		$phoneOptions = $xml->numbers->country->toArray();
		foreach ($phoneOptions as $key => $val)
		{
			if($val['code'] == $this->countryCode)
			{
				$this->options = $val;
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
		if($this->options['phoneLengthMin'] > $phoneLength || $phoneLength > $this->options['phoneLengthMax'])
		{
			return FALSE;
		}
		//$this->options['']
		$phoneArea = substr($phone, $this->options['areaPositionStart'], $this->options['areaLength']);
		$phonePrefix = substr($phone, $this->options['prefixPositionStart'], $this->options['prefixLength']);
		if ($phoneLength > $this->options['phoneLengthMin']) 
		{
			// internationalPrefix length is compared
			$intPrefixLength = strlen($this->options['internationalPrefix']);
			if(substr($phone, 0, $intPrefixLength) != $this->options['internationalPrefix'])
			{
				return FALSE;
			}
			$phoneArea = substr($phone, $this->options['areaPositionStart']+$intPrefixLength, $this->options['areaLength']);
			$phonePrefix = substr($phone, $this->options['prefixPositionStart']+$intPrefixLength, $this->options['prefixLength']);
			$conditionArea = '';
			$conditionPrefix = '';
			if(is_array($this->options['allow']))
			{
				$allowKey = key($this->options['allow']);	
				$conditionArea = ' && '.in_array(${'phone'.ucfirst($allowKey)}, $this->options['allow'][$allowKey]);
			}
			if(is_array($this->options['deny']))
			{
				$denyKey = key($this->options['deny']);
				$conditionPrefix = !in_array(${'phone'.ucfirst($denyKey)}, $this->options['deny'][$denyKey]);
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
}
