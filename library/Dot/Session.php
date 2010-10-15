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
* Session management  
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Session
{	
	/**
	 * Constructor is private, because this class is static, can't be instantiated
	 * @access private
	 * @return Dot_Session
	 */
	private function __construct ()
	{		
	}
	/**
	 * Start the session
	 * @access public
	 * @static
	 * @return void
	 */
	public static function start()
	{
		$option = Zend_Registry::get('option');
		//check is exists session for the current module
		if(isset($option->session))
		{
			$namespaceName = $option->session->name;
			$rememberMe = $option->session->rememberMeSeconds;
			//if session is not registered, create it
			if(!(Zend_Registry::isRegistered('session')))
			{
				$session = new Zend_Session_Namespace($namespaceName);
				if(!isset($session->initialized))
				{
					Zend_Session::regenerateId();
					$session->initialized = TRUE;
					Zend_Session::rememberMe($rememberMe);
				}
				Zend_Registry::set('session',$session);
			}
		}
		else
		{
			Zend_Registry::set('session',NULL);
		}		
	}
}