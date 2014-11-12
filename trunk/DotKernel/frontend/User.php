<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    Frontend
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * User Model
 * Here are all the actions related to the user
 * @category   DotKernel
 * @package    Frontend
 * @author     DotKernel Team <team@dotkernel.com>
 */

class User extends Dot_Model_User
{
	
	private $_userAgent;
	private $_httpReferer;
	
	/**
	 * Constructor
	 * @access public
	 */
	public function __construct($userAgent = NULL, $httpReferer=NULL)
	{
		parent::__construct();
		// if no userAgent is given on function call mark it as empty - if the userAgent is empty keep it empty
		// if the userAgent stays empty it can be used for robot detecting or devices with blank UA (usually bots)
		// HTTP Reffer is optional so mark it empty if there is no HTTP Reffer
		$this->_userAgent = (!is_null($userAgent)) ? $userAgent : '';
		$this->_httpReferer = (!is_null($httpReferer)) ? $httpReferer : '';
	}

	/**
	 * Get user info
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getUserInfo($id)
	{
		$select = $this->db->select()
						->from('user')
						->where('id = ?', $id);
		return $this->db->fetchRow($select);
	}

	/**
	 * Register logins data
	 * @access public
	 * @param array $data
	 * @return void
	 */
	public function registerLogin($data)
	{
		$this->db->insert('userLogin', $data);
	}

	/**
	 * Send a link to reset the  password to user's email
	 * @access public
	 * @param string $email
	 * @return void
	 */
	public function forgotPassword($email)
	{
		$session = Zend_Registry::get('session');
		$seo = Zend_Registry::get('seo');
		$value = $this->getUserBy('email', $email);

		if(!empty($value))
		{
			$dotEmail = new Dot_Email();
			$dotEmail->addTo($email);
			$subject = str_replace('%SITENAME%', $seo->siteName, $this->option->forgotPassword->subject);
			$dotEmail->setSubject($subject);
			$userToken = Dot_Auth::generateUserToken($value['password']);
			
			$msg = str_replace(array('%FIRSTNAME%', '%SITE_URL%', '%USERID%', '%TOKEN%'), 
													array($value['firstName'], $this->config->website->params->url, $value['id'], $userToken), 
													$this->option->forgotPassword->message);
			$dotEmail->setBodyText($msg);
			$succeed = $dotEmail->send();
			if($succeed)
			{
				$session->message['txt'] = $this->option->errorMessage->emailSent . $email;
				$session->message['type'] = 'info';
			}
			else
			{
				$session->message['txt'] = $this->option->errorMessage->emailNotSent . $email;
				$session->message['type'] = 'error';
			}
		}
		else
		{
			$session->message['txt'] = $email . $this->option->errorMessage->emailNotFound;
			$session->message['type'] = 'error';
		}
	}

	/**
	 * Authorize user login
	 * @access public
	 * @param array $validData
	 * @return void
	 */
	public function authorizeLogin($validData)
	{
		$session = Zend_Registry::get('session');
		unset($session->user);
		// login info are VALID, we can see if is a valid user now 
		$dotAuth = Dot_Auth::getInstance();
		$validAuth = $dotAuth->process('user', $validData);
		if($validAuth)
		{
			//prepare data for register the login
			$dotGeoip = new Dot_Geoip();
			$userIp = Dot_Kernel::getUserIp();
			$userCountry = $dotGeoip->getCountryByIp($userIp);
			$dataLogin = array( 'ip' => $userIp, 
								'userId' => $session->user->id, 
								'referer' => $this->_httpReferer,
								'userAgent' => $this->_userAgent,
								'country' => $userCountry[1]
								);
			$this->registerLogin($dataLogin);
			$link = isset($session->wantUrl) ? $session->wantUrl : $this->config->website->params->url.'/user/account';
			header('location: '.$link);
			exit;
		}
		else
		{
			$session->message['txt'] = $this->option->errorMessage->login;
			$session->message['type'] = 'error';
		}
	}
}