<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
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
	private function __construct()
	{
	}

	/**
	 * Start the session, using the session settings from application.ini and dots.xml
	 * @access public
	 * @static
	 * @return void
	 */
	public static function start()
	{
		$option = Zend_Registry::get('option');
		$config = Zend_Registry::get('configuration');
		
		//check is a session exists for the current module
		if(isset($option->session))
		{
			$namespaceName = $option->session->name;
			
			//if session is not registered, create it
			if(! (Zend_Registry::isRegistered('session')))
			{
				$session = new Zend_Session_Namespace($namespaceName);
				// set session options 
				Zend_Session::setOptions($config->resources->session->toArray());
				if(! isset($session->initialized))
				{
					$session->initialized = TRUE;
					// use only session cookie and regenerate session in the same time 
					Zend_Session::rememberMe($config->resources->session->remember_me_seconds);
				}
				Zend_Registry::set('session', $session);
			}
		}
		else
		{
			Zend_Registry::set('session', NULL);
		}
	}
}