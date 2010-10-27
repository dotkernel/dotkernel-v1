<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
* @copyright  Copyright (c) 2009-2010 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
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
	 * Check to see if user can login
	 * @access public
	 * @param array $data
	 * @return array
	 */
	public function checkLogin($data)
	{
		$password = md5($data['username'].$this->config->settings->admin->salt.$data['password']);
		$select = $this->db->select()
						   ->from('admin')
						   ->where('isActive = ?','1')
						   ->where('username = ?', $data['username'])
						   ->where('password = ?', $password);
						   
		$results = $this->db->fetchAll($select);	
		if( 1 == count($results))
		{
			return $results;
		}
		else
		{
			return array();
		}
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
	 * @return array(array(), Zend_Paginator_Adapter())
	 */
	public function getUserList($page = 1)
	{
		$select = $this->db->select()
						   ->from('admin');				
 		$paginatorAdapter = new Zend_Paginator_Adapter_DbSelect($select);
		($page == 1) ? 
			$select->limit($this->settings->resultsPerPage) : 
			$select->limit($this->settings->resultsPerPage, ($page-1)*$this->settings->resultsPerPage);
							
		$data = $this->db->fetchAll($select);
		return array('data'=> $data,'paginatorAdapter'=> $paginatorAdapter);
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
	 * Validate the data that comes from login form
	 * @access public
	 * @param string $username
	 * @param string $password
	 * @param string $send [optional] which is a control key
	 * @return bool
	 */
	public function validateLogin($username, $password, $send = 'off')
	{
		$login = array();
		$error = array(); 
		if ($send =='on')
		{
			$validatorUsername = new Zend_Validate();
			$validatorUsername->addValidator(new Zend_Validate_StringLength(
												$this->option->validate->username->lengthMin, 
												$this->option->validate->username->lengthMax
											))   
							  ->addValidator(new Zend_Validate_Alnum());
			if ($validatorUsername->isValid($username))
			{
				$login['username'] = $username;
			}
			else
			{
				$error['username'] = $this->option->errorMessage->invalidUsername;
				$login['username'] = '';
			}

			$validatorPassword = new Zend_Validate();
			$validatorPassword->addValidator(new Zend_Validate_StringLength(
												$this->option->validate->password->lengthMin, 
												$this->option->validate->password->lengthMax
											));
			if ($validatorPassword->isValid($password))
			{
				$login['password'] = $password;
			}
			else
			{
				$error['password'] = $this->option->errorMessage->invalidPassword;
				$login['password'] = '';
			}			
		}
		return array('login'=>$login, 'error'=>$error);
	}
	/**
	 * Validate user input, add or update form
	 * @access public
	 * @param array $values
	 * @param int $userId 
	 * @return array
	 */
	public function validateUser($values, $userId = 0)
	{
		$data = array();
		$error = array();
		//validate the input data - username, password and email will be also filtered
		$validatorChain = new Zend_Validate();
		//validate details parameters	
		if(array_key_exists('details', $values))
		{
			$validDetails = Dot_Kernel::validateFilter($validatorChain, $values['details']);
			$data = array_merge($data, $validDetails['data']);
			$error = array_merge($error, $validDetails['error']);
		}		
		//validate username
		if(array_key_exists('username', $values))
		{
			$validatorChain = new Zend_Validate();
			$validatorChain->addValidator(new Zend_Validate_Alpha())
						   ->addValidator(new Zend_Validate_StringLength(
											$this->option->validate->details->lengthMin, 
											$this->option->validate->details->lengthMax
										 ));
			$validUsername = Dot_Kernel::validateFilter($validatorChain, $values['username']);
			$data = array_merge($data, $validUsername['data']);
			$uniqueError = $this->_validateUnique('username', $values['username']['username'], $userId);
			$error = array_merge($error, $validUsername['error'], $uniqueError);
		}
		//validate email
		if(array_key_exists('email', $values))
		{
			$validatorEmail = new Zend_Validate_EmailAddress();		
			$validEmail = Dot_Kernel::validateFilter($validatorEmail, $values['email']);
			$data = array_merge($data, $validEmail['data']);
			$uniqueError = $this->_validateUnique('email', $values['email']['email'], $userId);
			$error = array_merge($error, $validEmail['error'], $uniqueError);
		}			
		//validate enum
		if(array_key_exists('enum', $values))
		{
			$validatorEnum = new Zend_Validate_InArray(explode(',', $values['enum'][0]));
			unset($values['enum'][0]);
			$validEnum = Dot_Kernel::validateFilter($validatorEnum, $values['enum']);
			$data = array_merge($data, $validEnum['data']);
			$error = array_merge($error, $validEnum['error']);
		}		
		//validate password				
		if(array_key_exists('email', $values) && ($values['password']['password'] != '' || $values['password']['password2'] != ''))
		{			
			if($values['password']['password'] == $values['password']['password2'])
			{
				unset($values['password']['password2']);
				$validatorChain = new Zend_Validate();
				$validatorChain->addValidator(new Zend_Validate_StringLength(
												$this->option->validate->password->lengthMin, 
												$this->option->validate->password->lengthMax
											));			
				$validPass = Dot_Kernel::validateFilter($validatorChain, $values['password']);
				$data = array_merge($data, $validPass['data']);
				$error = array_merge($error, $validPass['error']);	
			}
			else
			{
				$error['password'] = $this->option->errorMessage->passwordTwice;
			}
		}
		return array('data' => $data, 'error' => $error);
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
	 * @return array(array(), Zend_Paginator_Adapter())
	 */
	public function getLogins($id, $page = 1)
	{
		$select = $this->db->select()
					   ->from('adminLogin');
		if ($id > 0) 
		{
			$select->where('adminId = ?', $id);
		}
		$select->order('dateLogin DESC');
 		$paginatorAdapter = new Zend_Paginator_Adapter_DbSelect($select);
		($page == 1) ? 
			$select->limit($this->settings->resultsPerPage) : 
			$select->limit($this->settings->resultsPerPage, ($page-1)*$this->settings->resultsPerPage);
							
		$data = $this->db->fetchAll($select);
		return array('data'=> $data,'paginatorAdapter'=> $paginatorAdapter);
	}	
	/**
	 * Authorize user login
	 * @access public
	 * @param Zend_Validate $validate
	 * @return void
	 */
	public function authorizeLogin($validate)
	{
		$route = Zend_Registry::get('route');
		$session = Zend_Registry::get('session');
		if(!empty($validate['login']) && empty($validate['error']))
		{
			// login info are VALID, we can see if is a valid user now 
			$user = $this->checkLogin($validate['login']);
			if(!empty($user))
			{
				$session->admin = $user[0];
				//prepare data for register the login
				$dataLogin = array('ip' => Dot_Kernel::getUserIp(), 
							  'adminId' => $session->admin['id'], 
							  'username' => $session->admin['username'], 
							  'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
							  'userAgent' => $_SERVER["HTTP_USER_AGENT"]);
				$this->registerLogin($dataLogin);
				header('Location: '.$this->config->website->params->url.'/' .  $route['module'] );
				exit;
			}
			else
			{
				unset($session->admin);
				// check if account is inactive
				$adminTmp = $this->getUserBy('username',$validate['login']['username']);
				(1 == $adminTmp['isActive']) ?
					$session->message['txt'] = $this->option->errorMessage->wrongCredentials:
					$session->message['txt'] = $this->option->errorMessage->inactiveAcount;
				$session->message['type'] = 'error';
			}
		}
		else
		{
			// login info are NOT VALID
			$txt = array();
			$field = array('username', 'password');
			foreach ($field as $v)
			{
				if(array_key_exists($v, $validate['error']))
				{
					 $txt[] = $validate['error'][$v];
				}
			}
			$session->message['txt'] = $txt;
			$session->message['type'] = 'error';
		}		
	}
	/**
	 * Check if user already exists - email, username, and return error
	 * @access private
	 * @param string $field
	 * @param string $value
	 * @param id $userId
	 * @return array
	 */
	private function _validateUnique($field, $value, $userId)
	{
		$error = array();
		//email is unique, check if exists
		$exists = $this->getUserBy($field, $value);
		if($userId > 0)
		{
			$currentUser = $this->getUserBy('id', $userId);				
			$uniqueCondition = (is_array($exists) && $exists[$field] != $currentUser[$field]);
		}
		else
		{
			$uniqueCondition = (FALSE != $exists);
		}			
		if($uniqueCondition)
		{
			$error[] = $value . $this->option->errorMessage->userExists;
		}
		return $error;
	}
}