<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Frontend
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
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

class User
{
	/**
	 * Constructor
	 * @access public 
	 * @return User
	 */
	public function __construct()
	{		
		$this->db = Zend_Registry::get('database');
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
		$select = $this->db->select()
						   ->from('user')
						   ->where('isActive = ?','1')
						   ->where('username = ?', $data['username'])
						   ->where('password = ?', $data['password']);
		$result = $this->db->fetchAll($select);
		( 1 == count($result)) ? $return = $result[0] : $return = array();
		return $return;
	}	
	/**
	 * Get user by field
	 * @access public
	 * @param string $field
	 * @param string $value
	 * @return array
	 */
	public function getUserBy($field = '', $value = '')
	{		
		$select = $this->db->select()
					   ->from('user')
					   ->where($field.' = ?', $value)
					   ->limit(1);					   
		$result = $this->db->fetchRow($select);
		return $result;
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
	 * Add new user
	 * @access public
	 * @param array $data
	 * @return void
	 */
	public function addUser($data)
	{		
		// if you want to add an inactive user, un-comment the below line, default: isActive = 1
		// $data['isActive'] = 0;
		$this->db->insert('user',$data);		
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
		$this->db->update('user', $data, 'id = '.$id);
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
			$validatorUsername->addValidator(new Zend_Validate_Alnum())
							  ->addValidator(new Zend_Validate_StringLength(
												$this->option->validate->username->lengthMin, 
												$this->option->validate->username->lengthMax
											));
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
	 * $values is an array on multiple levels. On first level, the key suggest what validation will be done.
	 * - details 	- only filter the input
	 * - username	- validate with Zend_Validate_Alnum, Zend_Validate_StringLength and filter the input
	 * - email		- validate with Zend_Validate_EmailAddress and filter the input 
	 * - password	- validate with Zend_Validate_StringLength and filter the input
	 * @access public
	 * @param array $values 
	 * @return array
	 */
	public function validateUser($values)
	{$data = array();
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
			$validatorChain->addValidator(new Zend_Validate_Alnum())
						   ->addValidator(new Zend_Validate_StringLength(
													$this->option->validate->details->lengthMin, 
													$this->option->validate->details->lengthMax
												));
			$validUsername = Dot_Kernel::validateFilter($validatorChain, $values['username']);
			$data = array_merge($data, $validUsername['data']);
			$error = array_merge($error, $validUsername['error']);
		}
		//validate email
		if(array_key_exists('email', $values))
		{
			$validatorEmail = new Zend_Validate_EmailAddress();		
			$validEmail = Dot_Kernel::validateFilter($validatorEmail, $values['email']);
			$data = array_merge($data, $validEmail['data']);
			$error = array_merge($error, $validEmail['error']);
		}	
		//validate password				
		if(array_key_exists('password', $values))
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
	 * Validate email user input
	 * @access public
	 * @param string $email
	 * @return array
	 */
	public function validateEmail($email)
	{
		$data = array();
		$error = array();
		$validatorEmail = new Zend_Validate_EmailAddress();		
		$validEmail = Dot_Kernel::validateFilter($validatorEmail, array('email'=>$email));
		return $validEmail;
	}
	/**
	 * Send forgot password to user
	 * @acess public
	 * @param string $email
	 * @return void
	 */
	public function forgotPassword($email)
	{
		$session = Zend_Registry::get('session');
		$value = $this->getUserBy('email', $email);
		if(!empty($value))
		{
			$dotEmail = new Dot_Email();
			$dotEmail->addTo($email);
			$dotEmail->setSubject($this->option->forgotPassword->subject);
			$msg = str_replace(array('%FIRSTNAME%', '%PASSWORD%'), 
							   array($value['firstName'], $value['password']), 
				              $this->option->forgotPassword->message);
			$dotEmail->setBodyText($msg);			
			$succeed = $dotEmail->send();
			if($succeed)
			{
				$session->message['txt'] = $this->option->errorMessage->emailSent.$email;
				$session->message['type'] = 'info';
			}
			else
			{
				$session->message['txt'] = $this->option->errorMessage->emailNotSent.$email;
				$session->message['type'] = 'error';
			}		
		}
		else
		{
			$session->message['txt'] = $email.$this->option->errorMessage->emailNotFound;
			$session->message['type'] = 'error';
		}		
	}	
}