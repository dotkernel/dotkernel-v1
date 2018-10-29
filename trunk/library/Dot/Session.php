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
			$namespacePrefix = 'dotkernel';
			if(isset($config->session->namespace_prefix) && is_string($config->session->namespace_prefix))
			{
				$namespacePrefix = $config->session->namespace_prefix;
			}
			
			$namespaceName = $option->session->name;
			//if session is not registered, create it
			if(! (Zend_Registry::isRegistered('session')))
			{
				// set session options 
				Zend_Session::setOptions($config->resources->session->toArray());
				
				// use only session cookie and regenerate session in the same time
				Zend_Session::rememberMe($config->resources->session->remember_me_seconds);
				
				//Until PHP 7.2 , this warning was silently ignore. First you need to set the parameters
				// and only after that, we can start the session
				$session = new Zend_Session_Namespace($namespaceName);
				
				if(! isset($session->initialized))
				{
					$session->initialized = true;
				}
				Zend_Registry::set('session', $session);
			}
		}
		else
		{
			Zend_Registry::set('session', null);
		}
	}
}