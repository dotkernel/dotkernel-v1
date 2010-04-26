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
* Authorize user methods, used in all DotKernel Applications 
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Authorize
{
	/**
	 * Constructor
	 * @access public
	 */
	public function __construct()
	{
		$this->config = Zend_Registry::get('configuration');
		$this->settings = Zend_Registry::get('settings');
		$this->db = Zend_Registry::get('database');
	}
	/**
	 * Validate the data that comes from login form
	 * @access public
	 * @static
	 * @param string $username
	 * @param string $password
	 * @param string $send [optional] which is a control key
	 * @return bool
	 */
	public static function validateLogin($username, $password, $send = 'off')
	{
		$login = array();
		$error = array(); 
		if ($send =='on')
		{
			$validatorUsername = new Zend_Validate();
			$validatorUsername->addValidator(new Zend_Validate_StringLength(3, 255))
			->addValidator(new Zend_Validate_Alnum());
			if ($validatorUsername->isValid($username))
			{
				$login['username'] = $username;
			}
			else
			{
				$error['username'] = 'Invalid Username. ';
				$login['username'] = '';
			}

			$validatorPassword = new Zend_Validate();
			$validatorPassword->addValidator(new Zend_Validate_StringLength(3, 25))
			->addValidator(new Zend_Validate_Alnum());
			if ($validatorPassword->isValid($password))
			{
				$login['password'] = $password;
			}
			else
			{
				$error['password'] = 'Invalid Password.';
				$login['password'] = '';
			}			
		}
		return array('login'=>$login, 'error'=>$error);
	}
	/**
	 * Check to see if user is logged in or not .
	 * @access public
	 * @static
	 * @param string $who [optional]
	 * @return bool
	 */
	public static function isLogin($who='user')
	{
		$session = Zend_Registry::get('session');
		if(isset($session->$who) && !empty($session->$who))
		{
			return true;
		}
		return false;
	}
	/**
	 * Check permission to a certain page/content 
	 * @access public
	 * @static
	 * @param Zend_Config $config
	 * @param string $who [optional]
	 * @TODO extension to check user level
	 * @return bool
	 */
	public static function checkPermissions($config, $who='user')
	{
		$session = Zend_Registry::get('session');
		switch($who)
		{
			case 'user':
		  	if( !self::isLogin('user') )
				{
					$session->loginUserError = "You don't have enough credentials to access this url.";
					header('Location: ' . $config->website->params->url . '/user/login');
					exit;
				}
				return true;
		  break;
			case 'admin':
		  	if( !self::isLogin('admin') )
				{
					$session->loginUserError = "You don't have enough credentials to access this url.";
					header('Location: ' . $config->website->params->url . '/'. $who);
					exit;
				}
				return true;
			break;
		  default:
			return false;
		}		
	}
	/**
	 * Logout the user
	 * Unset user session
	 * @param string $who [optional]
	 * @return void
	 */
	public static function logout($who='user')
	{
		$session = Zend_Registry::get('session');
		if(self::isLogin($who))
		{
			unset($session->$who);
		}
	}
	
}