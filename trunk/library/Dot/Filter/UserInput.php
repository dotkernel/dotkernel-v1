<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @copyright  Copyright (c) 2009-2016 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

 /**
 * Filter User Input
 * 
 * This class is used for filtering user-inputted data
 * It must be used either before/after validating data or when showing it (the data)
 * This class methods are static because they can be used anywhere
 * 
 * 
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotFilter
 * @see		   Dot_Filter
 * @author     DotKernel Team <team@dotkernel.com>
 */
class Dot_Filter_UserInput
{

	/**
	 * PHP's original sanitization options
	 * 
	 * @see: http://php.net/manual/ro/filter.filters.sanitize.php
	 * 
	 * @access private
	 * @var array
	 */
	private static $_phpSanitizeOptions = array(
			'email',
			'encoded',
			'magic_quotes',
			'number_float',
			'number_int',
			'special_chars',
			'fullspecial_chars',
			'string',
			'stripped',
			'url',
			'unsafe_raw'
	);
	
	/**
	 * Parse HTML characters
	 * 
	 * @access private
	 * @static
	 * @param string $string
	 * @return string
	 */
	private static function _sanitizeHtmlChars($string)
	{
		return htmlspecialchars($string);
	}
	
	/**
	 * Trim input spaces
	 * @param string $string
	 * @return string
	 */
	private static function _sanitizeTrimSpaces($string)
	{
		return trim($string);
	}
	
	/**
	 * Remove spaces from string
	 * 
	 * @param string $string
	 * @return string
	 */
	private static function _sanitizeNoSpaces($string)
	{
		return str_replace(' ', '', $string);
	}
	
	/**
	 * Sanitize using PHP sanitization functions
	 * 
	 * $filterSanitizeString must be a sanitization Name or ID
	 * @see: http://php.net/manual/ro/filter.filters.sanitize.php 
	 * 
	 * @param string $string - input string
	 * @param string $filterSanitizeString - the sanitization option
	 * 
	 * @return string
	 */
	private static function _sanitizeFilter($string, $filterSanitizeString)
	{
		if(in_array($filterSanitizeString, self::$_phpSanitizeOptions))
		{
			return filter_var($string, $filterSanitizeString);
		}
		return $string;
	}
	
	
	/**
	 * Parse String with given options
	 *
	 * @access public
	 * @static
	 * @param string $string
	 * @param array $options [optional]
	 * @return string
	 */
	public static function sanitizeString($string, $options = array('htmlChars', 'trimSpaces'))
	{
		foreach($options as $sanitizeType)
		{
			$sanitizeFunction = '_sanitize' . ucfirst($sanitizeType);
			if(method_exists('Dot_Filter_UserInput', $sanitizeFunction))
			{
				$string = self::$sanitizeFunction($string);
			}
			else
			{
				self::_sanitizeFilter($string, $sanitizeType);
			}
			
		}
		return $string;
	}

	/**
	 * Parse array RECURSIVELY with given options
	 * 
	 * This function is used to parse all the data within an array
	 *  without modifying its structure
	 *  
	 *  
	 *  Sanitization options:
	 *  from this class:
	 *  htmlChars, noSpaces, trimSpaces
	 *   
	 *  from PHP:
	 *  email encoded magic_quotes number_float number_int special_chars 
	 *  fullspecial_chars string stripped url unsafe_raw
	 *
	 * If no sanitization is found within class, PHP sanitization options will be searched 
	 * If no PHP sanitization option is found, the string will be returned, and the error will be logged  
	 * 
	 * Default sanitization options:
	 * htmlChars, trimSpaces
	 * 
	 * @access public
	 * @static
	 * @param string $string
	 * @param array $options [optional]
	 * @return string
	 */
	public static function sanitizeArray($array, $options = array('htmlChars', 'trimSpaces'))
	{
		$sanitizedArray = array();
		
		foreach($array as $key => $value)
		{
			if(! is_array($value))
			{
				$sanitizedArray[$key] = self::sanitizeString($value, $options);
			}
			else
			{
				$sanitizedArray[$key] = self::sanitizeArray($value, $options);
			}
		}
		return $sanitizedArray;
	}
}
