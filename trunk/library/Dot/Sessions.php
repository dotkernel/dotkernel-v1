<?php
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
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
	 * Constructor
	 * @access public
	 * @return Dot_Sessions
	 */
	public function __construct ()
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
		Zend_Session::start();
		if(!(Zend_Registry::isRegistered('session')))
		{
			$session = new Zend_Session_Namespace('kernel');
			if(!isset($session->initialized))
			{
				Zend_Session::regenerateId();
				$session->initialized = TRUE;
			}
			Zend_Registry::set('session',$session);
		}
	}
}