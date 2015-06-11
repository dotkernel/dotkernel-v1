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
 * Dot_Request
 * 
 * Used in order to process the Request Data
 * The Dot_Request also allows access to the Request Data from Models or Views 
 * (note: you should NOT use $_SERVER, $_GET, $_POST in Model, View Classes or in Library)
 * 
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotRequest
 * @author     DotKernel Team <team@dotkernel.com>
 */

class Dot_Request
{
	// server get & post will be set to false
	// so that we know if they were set
	/**
	 * $_SERVER placeholder
	 * @access protected
	 * @static
	 * @var array|bool
	 */
	protected static $_server  = false;
	
	/**
	 * $_POST placeholder
	 * @access protected
	 * @static
	 * @var array|bool
	 */
	protected static $_post    = false;
	
	/**
	 * $_GET placeholder
	 * @access protected
	 * @static
	 * @var array|bool
	 */
	protected static $_get     = false;
	
	/**
	 * Used to prevent multiple writing of request data
	 * @access protected
	 * @static
	 * @var bool
	 */
	private static $_isDataSet = false;
	
	/**
	 * Sets the Server Request Parameters
	 * 
	 * Singleton-ish implementation
	 * We should only write theese values once
	 * 
	 * @param array $server - $_SERVER
	 * @param array $get    - $_GET
	 * @param array $post   - $_POST
	 * @return bool
	 */
	public static function setRequestData($server, $get, $post)
	{
		// this could also have an exception thrown
		// but calling a function that only checks a value and returns false is harmless (and costless) 
		if(self::$_isDataSet != true )
		{
			self::$_server = $server;
			self::$_get  = $get;
			self::$_post = $post;
			self::$_isDataSet = true;
			return true;
		}
		return false;
	}
	
	/**
	 * Get Request Data ( $_SERVER, $_GET, $_POST ) 
	 *
	 * Use with caution, the $_SERVER array is big
	 * 
	 * @static
	 * @access public
	 * @return array
	 */
	public static function getRequestData()
	{
		return array(
			'server' => self::$_server,
			'get' => self::$_get,
			'post' => self::$_post,
		);
	}

	/**
	 * Get the Request User Agent
	 * 
	 * Returns the User Agent found in $_SERVER
	 * Returns bool false if value was not found 
	 * 
	 * This function was implemented because
	 * it is called frequently
	 * Returns the HTTP_USER_AGENT and
	 * false if it doesn't exist
	 * 
	 * @access public 
	 * @return string|bool $userAgent
	 */
	public static function getUserAgent()
	{
		if(isset(self::$_server['HTTP_USER_AGENT']))
		{
			return self::$_server['HTTP_USER_AGENT'];
		}
		return false;
	}
	
	/**
	 * Get the Request User Agent
	 *
	 * This function was implemented because
	 * it is called frequently
	 * Returns the HTTP_REFFER and
	 * false if it doesn't exist
	 *
	 * @access public
	 * @return string|bool
	 */
	public static function getHttpReffer()
	{
		if(isset(self::$_server['HTTP_REFERER']))
		{
			return self::$_server['HTTP_REFERER'];
		}
		return false;
	}
}