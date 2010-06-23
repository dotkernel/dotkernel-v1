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
* Authorize user methods, used in all DotKernel Applications
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Auth
{        			
	/**
	 * Check permission to a certain page/content 
	 * Set wanted url if user is not logged
	 * @access public
	 * @static
	 * @todo extension to check user level
	 * @return bool
	 */
	public static function checkIdentity($who='user')
	{		
		$config = Zend_Registry::get('configuration');
		$session = Zend_Registry::get('session');
		if(!self::hasIdentity($who))
		{
			//register wanted url
			if(!isset($session->wantUrl))
			{
				$dotSeo = new Dot_Seo();
				$session->wantUrl = $dotSeo->createCanonicalUrl();
			}
			if(Zend_Registry::isRegistered('option'))
			{
				$option = Zend_Registry::get('option');
				$session->message['txt'] = $option->warningMessage->userPermission;
				$session->message['type'] = 'warning';
			}	
			//create login url	
			switch ($who) 
			{
				case 'admin':
					$loginUrl = $config->website->params->url . '/admin/admin/login';	
				break;				
				default:
					$loginUrl = $config->website->params->url . '/' . $who . '/login';
				break;
			}
			header('Location: ' . $loginUrl);
			exit;
		}
		//redirect user to wanted url
		if(isset($session->wantUrl))
		{
			$wantUrl = $session->wantUrl;
			unset($session->wantUrl);
			header('Location: ' . $wantUrl);
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
	public static function reguireLogin($who)
	{
		$session = Zend_Registry::get('session');
		if(!isset($session->wantUrl))
		{
			$dotSeo = new Dot_Seo();
			$session->wantUrl = $dotSeo->createCanonicalUrl();
		}
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