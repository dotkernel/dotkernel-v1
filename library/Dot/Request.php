<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotLibrary
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * Process the Request Data
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotRequest
 * @author     DotKernel Team <team@dotkernel.com>
 */

class Dot_Request
{
	protected static $_server;
	protected static $_get; 
	protected static $_post;
	private static $_isDataSet = false ;
	
	/**
	 * Singleton
	 * @todo test it with upload of some 20 mb file  
	 * @todo add constructor ?
	 * Sets the Server Request Parameters
	 * @param array $server $_SERVER
	 * @param array $get $_GET
	 * @param array $post $_POST
	 * @return void
	 */
	public static function setRequestData($server, $get = array(), $post = array())
	{
		if(self::$_isDataSet != true )
		{
			self::$_server = $server;
			self::$_get  = $get;
			self::$_post = $post;
			self::$_isDataSet = true;
		}
	}
	
	/**
	 * Get Request Data
	 * @static
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
	 * This function was implemented because
	 * it is called frequently 
	 * 
	 * @access public 
	 * @return string $userAgent
	 */
	public static function getUserAgent()
	{
		if(isset(self::$_server['HTTP_USER_AGENT']))
			return self::$_server['HTTP_USER_AGENT'];
		return '';
	}
	
	/**
	 * Get the Request User Agent
	 *
	 * This function was implemented because
	 * it is called frequently
	 *
	 * @access public
	 * @return string $userAgent
	 */
	public static function getHttpReffer()
	{
		if(isset(self::$_server['HTTP_REFERER']))
			return self::$_server['HTTP_REFERER'];
		return '';
	}
}