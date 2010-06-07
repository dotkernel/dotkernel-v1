<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id: Authorize.php 105 2010-03-15 01:39:17Z julian $
*/

/**
* Authorize user methods, used in all DotKernel Applications
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Auth
{        			
	/**
	 * Check permission to a certain page/content 
	 * @access public
	 * @static
	 * @TODO extension to check user level
	 * @return bool
	 */
	public static function checkIdentity($who='user')
	{
		$session = Zend_Registry::get('session');
		if(!self::hasIdentity($who))
		{
			$option = Zend_Registry::get('option');
			$config = Zend_Registry::get('configuration');
			$session->message['txt'] = $option->warningMessage->userPermission;
			$session->message['type'] = 'warning';
			header('Location: ' . $config->website->params->url . '/' . $who . '/login');
			exit;
		}
		return true;
	}
	/**
	 * Check to see if identity exists - is log in
	 * @access public
	 * @static
	 * @param string $who [optional]
	 * @return bool
	 */	
	public static function hasIdentity($who='user')
	{
		$session = Zend_Registry::get('session');
		if(isset($session->$who) && !empty($session->$who))
		{
			return true;
		}
		return false;
	}	
	/**
	 * Return identity of $who
	 * @access public
	 * @static
	 * @param string $who [optional]
	 * @return object
	 */
	public static function getIdentity($who='user')
	{
		$session = Zend_Registry::get('session');
		if(self::hasIdentity($who))
		{
			return $session->$who;
		}
		return NULL;
	}
	/**
	 * Clear the identity - log out
	 * @access public
	 * @static
	 * @param string $who [optional]
	 * @return void
	 */
	public static function clearIdentity($who='user')
	{
		$session = Zend_Registry::get('session');
		if(self::hasIdentity($who))
		{
			unset($session->$who);
		}
	}
       
}

