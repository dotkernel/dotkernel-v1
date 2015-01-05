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
 * Authorize user methods, used in all DotKernel Applications
 * @category   DotKernel
 * @package    DotLibrary
 * @author     DotKernel Team <team@dotkernel.com>
 */

class Dot_Auth
{

	/**
	 * Singleton instance
	 * @access protected
	 * @static
	 * @var Dot_Auth
	 */
	protected static $_instance = null;

	/**
	 * Singleton pattern implementation makes 'new' unavailable
	 * @access protected
	 * @return void
	 */
	protected function __construct()
	{
		$this->_identity = null;
		$this->acl = new Dot_Acl();
		$this->setRoles($this->acl->getRoles());
	}

	/**
	 * Singleton pattern implementation makes 'clone' unavailable
	 * @access protected
	 * @return void
	 */
	protected function __clone()
	{}

	/**
	 * Returns an instance of Dot_Auth
	 * Singleton pattern implementation
	 * @access public
	 * @static
	 * @return Dot_Auth
	 */
	public static function getInstance()
	{
		if (null === self::$_instance)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Set the roles for authentification
	 * @access public
	 * @param array $roles
	 * @return  void
	 */
	public function setRoles($roles)
	{
		krsort($roles);
		$this->_roles = $roles;
	}

	/**
	 * Check permission based on the ACL roles
	 * Set wanted url if user is not logged
	 * @todo extension to check user level
	 * @access public
	 * @param string $who - who is checking the identity
	 * @return bool
	 */
	public function checkIdentity($who)
	{
		$role = 'guest';
		if($this->hasIdentity())
		{
			$user = $this->getIdentity();
			if(is_object($user))
			{
				$role = $user->role;
			}
		}
		$config = Zend_Registry::get('configuration');
		$session = Zend_Registry::get('session');
		if(!$this->acl->isAllowed($role))
		{
			//register wanted url
			if(!isset($session->wantUrl))
			{
				$session->wantUrl = Dot_Route::createCanonicalUrl();
			}
			$option = Zend_Registry::get('option');
			if(isset($option->warningMessage->userPermission))
			{
				$session->message['txt'] = $option->warningMessage->userPermission;
				$session->message['type'] = 'warning';
			}
			//create login url	to which will be redirect
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

		//if user is allowed, redirect him to wanted url
		if($role == 'admin' && isset($session->wantUrl))
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
	 * @return bool
	 */
	public function hasIdentity()
	{
		$session = Zend_Registry::get('session');
		foreach ($this->_roles as $who)
		{
			if(isset($session->$who) && !empty($session->$who))
			{
				$session->$who->role = $who;
				$this->_identity = $session->$who;
				return true;
			}
		}
		return false;
	}

	/**
	 * Return identity
	 * @access public
	 * @return object
	 */
	public function getIdentity()
	{
		return $this->_identity;
	}

	/**
	 * Clear the identity - log out
	 * @access public
	 * @static
	 * @param string $who [optional]
	 * @return void
	 */
	public function clearIdentity($who = 'user')
	{
		$session = Zend_Registry::get('session');
		if(isset($session->$who) && !empty($session->$who))
		{
			unset($session->$who);
			$this->_identity = null;
			unset($session->wantUrl);
		}
	}

	/**
	 * Process the authentification with Zend_Auth. Unifying now the login procedure for both admin and user
	 * Return TRUE or FALSE if succedded or not
	 * @access public
	 * @param string $who - who to authentificate (admin, user)
	 * @param array $values - username and password to process
	 * @param bool $storeInSession - should the result be stored in the session?
	 * @return boolean
	 */
	public function process($who, $values, $storeInSession = true)
	{
		// Removing the old Zend_Auth object and use direct verification of passwords
		//1. retrieve user information as an object , or FALSE if nothing
		$userInfo = $this->_getUserInformation($who, $values['username']);
		
		//We have no such user? Hurry Up and return FALSE 
		if(!$userInfo)
		{
			return false;
		}
		
		$auth = $this->_authenticate($userInfo, $values['password']);
		if(true == $auth)
		{
			if ($storeInSession)
			{
				$session = Zend_Registry::get('session');
				// will store in the session object $session->admin or $session->user
				// all data from table row as stdClass Object
				$session->$who = $userInfo;
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Private function to authenticate some user information against a provided password 
	 * @param object $userInfo
	 * @param string $password
	 * @return boolean
	 */
	private function _authenticate($userInfo, $password)
	{
		$passwordApi = new Dot_Password();
		return $passwordApi->verifyPassword($password, $userInfo->password);
	}
	
	/**
	 * Retrive all information about a user, using its userType and username
	 * @param string $username
	 * @param string $userType
	 * @return object
	 */
	private function _getUserInformation($userType, $username)
	{
		$returnObject = new stdClass();
		
		$this->db = Zend_Registry::get('database');
		$select = $this->db->select()->from($userType)->where('username = ?', $username)->where('isActive = ?','1');
		$resultArray = $this->db->fetchRow($select);
		
		// No results, we don't have that username
		if(!is_array($resultArray))
		{
			return false;
		}
		
		// Backward compatibility with Zend_Auth class, getResultRowObject method need to return an object instead of array
		foreach($resultArray as $resultColumn => $resultValue)
		{
			$returnObject->{$resultColumn} = $resultValue;
		}
		return $returnObject;
	}

	/**
	 * Generate a token for a user
	 * @access public
	 * @static
	 * @param string $password - the users's password or password hash
	 * @return array
	 */
	public static function generateUserToken($password)
	{
		$config = Zend_Registry::get('configuration');
		// use the user's password hash and the site database password
		return sha1($config->database->params->password . $password);
	}

	/**
	 * Check if a user's token is set and is correct
	 * @access public
	 * @static
	 * @param string $userToken
	 * @param string $type - the identity that is checked (i.e. admin)
	 * @return void
	 */
	public static function checkUserToken($userToken, $type='admin')
	{
		if(is_null($userToken) || $userToken == '' )
		{
			return null;
		}
		$dotAuth = Dot_Auth::getInstance();
		$user = $dotAuth->getIdentity($type);
		if ((Dot_Auth::generateUserToken($user->password) != $userToken))
		{
			return null;
		}
		return true;
	}
}