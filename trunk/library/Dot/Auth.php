<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
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
			$option = Zend_Registry::get('option');
			if(isset($option->warningMessage->userPermission))
			{				
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
	/**
	 * Process the authentification with Zend_Auth.
	 * Return TRUE or FALSE if succedded or not
	 * @access public
	 * @static
	 * @param string $who
	 * @param array $values
	 * @return bool
	 */
	public static function process($who, $values)
	{
		$adapter = self::getAuthAdapter($who);
		$adapter->setIdentity($values['username']);
		$adapter->setCredential($values['password']);
		$adapter->getDbSelect()->where('isActive = ?','1');
		if('admin' == $who)
		{
			$config = Zend_Registry::get('configuration');
			$password = md5($values['username'].$config->settings->admin->salt.$values['password']);
			$adapter->setCredential($password);
		}		
		$auth = Zend_Auth::getInstance();
		$result = $auth->authenticate($adapter);
		if($result->isValid())
		{
			$session = Zend_Registry::get('session');
			$session->$who = $adapter->getResultRowObject();
			return TRUE;
		}		
		return FALSE;
	}
	/**
	 * Get the auth adapter
	 * @access public
	 * @static
	 * @param string $who
	 * @return Zend_Auth_Adapter_DbTable
	 */
	public static function getAuthAdapter($who)
	{
		$dbAdapter = Zend_Registry::get('database');	
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		$authAdapter->setTableName($who)
			->setIdentityColumn('username')
			->setCredentialColumn('password');					
		return $authAdapter;
	}
	
	     
}