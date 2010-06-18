<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Session management  
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Sessions
{	
	/**
	 * Constructor is private, because this class is static, can't be instantiated
	 * @access private
	 * @return Dot_Sessions
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
	public static function start($module)
	{
		$resource = Zend_Registry::get('resource');
		$namespaceName = $resource->session->$module->name;
		$rememberMe = $resource->session->$module->rememberMeSeconds;
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
}