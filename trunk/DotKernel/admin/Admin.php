<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
* @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Admin Model
* Here are all the actions related to the user
* @category   DotKernel
* @package    Admin 
* @author     DotKernel Team <team@dotkernel.com>
*/

class Admin
{	
	/**
	 * Constructor
	 * @access public
	 */
	public function __construct()
	{		
		$this->db = Zend_Registry::get('database');
		$this->config = Zend_Registry::get('configuration');
		$this->settings = Zend_Registry::get('settings');
		$this->option = Zend_Registry::get('option');	
	}		
	/**
	 * Get admin by field
	 * @access public
	 * @param string $field
	 * @param string $value
	 * @return array
	 */
	public function getUserBy($field = '', $value = '')
	{		
		$select = $this->db->select()
					   ->from('admin')
					   ->where($field.' = ?', $value)
					   ->limit(1);					   
		$result = $this->db->fetchRow($select);
		return $result;
	}
	/**
	 * Get user list
	 * @access public 
	 * @param int $page [optional]
	 * @return array
	 */
	public function getUserList($page = 1)
	{
		$select = $this->db->select()
						   ->from('admin');	
 		$dotPaginator = new Dot_Paginator($select, $page, $this->settings->resultsPerPage);
		return $dotPaginator->getData();
	}
	/**
	 * Add new user
	 * @access public
	 * @param array $data
	 * @return void
	 */
	public function addUser($data)
	{		
		// if you want to add an inactive user, un-comment the below line, default: isActive = 1
		// $data['isActive'] = 0;
		$data['password'] = md5($data['username'].$this->config->settings->admin->salt.$data['password']);
		$this->db->insert('admin', $data);		
	}	
	/**
	 * Update user
	 * @access public
	 * @param array $data
	 * @return void
	 */
	public function updateUser($data)
	{
		$id = $data['id'];
		unset ($data['id']);
		if(array_key_exists('password', $data))
		{
			$user = $this->getUserBy('id', $id);
			$data['password'] = md5($user['username'].$this->config->settings->admin->salt.$data['password']);
		}
		$this->db->update('admin', $data, 'id = ' . $id);
	}	
	/**
	 * Delete admin user
	 * @access public
	 * @param int $id
	 * @return void
	 */
	public function deleteUser($id)
	{
		$this->db->delete('admin', 'id = ' . $id);
	}
	/**
	 * Update active field for admin user
	 * @access public 
	 * @param int $id
	 * @param int $isActive
	 * @return void
	 */
	public function activateUser($id, $isActive)
	{		
		$this->db->update('admin', array('isActive' => $isActive), 'id = '.$id);
	}
	/**
	 * Register logins data
	 * @access public 
	 * @param array $data
	 * @return void
	 */
	public function registerLogin($data)
	{
		$this->db->insert('adminLogin', $data);
	}
	/**
	 * Get admin users logins archive list
	 * @access public
	 * @param int $id 
	 * @param int $page [optional]
	 * @return array
	 */
	public function getLogins($id, $page = 1)
	{
		$select = $this->db->select()
						->from('adminLogin')
						->joinLeft(
							'admin',
							'adminLogin.adminId=admin.id',
							'username'
						);
		if ($id > 0) 
		{
			$select->where('adminId = ?', $id);
		}
		$select->order('dateLogin DESC');
 		$dotPaginator = new Dot_Paginator($select, $page, $this->settings->resultsPerPage);
		return $dotPaginator->getData();
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
		unset($session->admin);		
		// login info are VALID, we can see if is a valid user now	
		$dotAuth = Dot_Auth::getInstance();
		$validAuth = $dotAuth->process('admin', $validData);
		if($validAuth)
		{
			//prepare data for register the login
			$dataLogin = array('ip' => Dot_Kernel::getUserIp(), 
							   'adminId' => $session->admin->id,
							   'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
							   'userAgent' => $_SERVER["HTTP_USER_AGENT"]);
			$this->registerLogin($dataLogin);
			$route = Zend_Registry::get('route');
			header('Location: '.$this->config->website->params->url.'/' .  $route['module'] );
			exit;
		}			
		else
		{	// failed admin login - send email to valid admin account
			$this->sendEmailFailedLogin();		
			// check if account is inactive
			$adminTmp = $this->getUserBy('username',$validData['username']);
			(1 == $adminTmp['isActive']) ?
				$session->message['txt'] = $this->option->errorMessage->wrongCredentials:
				$session->message['txt'] = $this->option->errorMessage->inactiveAcount;
			$session->message['type'] = 'error';
		}		
	}
	/**
	 * Failed admin login - send email notice to valid admin account
	 * @access private
	 * @return void
	 */
	private function sendEmailFailedLogin()
	{
			
		$this->seo = Zend_Registry::get('seo');		
		//get the email of the oldest valid admin account
		$select = $this->db->select()->from('admin', 'email')->where('isActive = ?', '1')->order('id asc')->limit(1);
		$emailAdmin = $this->db->fetchOne($select);
		$dotEmail = new Dot_Email();
		$dotEmail->addTo($emailAdmin);
		$dotEmail->setSubject($this->seo->siteName . ' - ' . $this->option->failedLogin->subject);
		$dotGeoip = new Dot_Geoip();
		$country = $dotGeoip->getCountryByIp(Dot_Kernel::getUserIp());
		$msg = str_replace(array('%DATE%', '%COUNTRY%', '%IP%', '%USERAGENT%'), 
						   array(Dot_Kernel::timeFormat('now', 'long'), $country[1], Dot_Kernel::getUserIp(), $_SERVER['HTTP_USER_AGENT']), 
			              $this->option->failedLogin->message);
		$dotEmail->setBodyText($msg);		
		$succeed = $dotEmail->send();			
	}
}