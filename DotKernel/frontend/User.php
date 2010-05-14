<?php
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
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
		$this->config = Zend_Registry::get('configuration');
		$this->settings = Zend_Registry::get('settings');
		$this->scope = Zend_Registry::get('scope');		
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
		// if you want to add an inactive user, un-comment the below line
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
												$this->scope->validate->username->lengthMin, 
												$this->scope->validate->username->lengthMax
											))   
							  ->addValidator(new Zend_Validate_Alnum());
			if ($validatorUsername->isValid($username))
			{
				$login['username'] = $username;
			}
			else
			{
				$error['username'] = $this->scope->errorMessage->invalidUsername;
				$login['username'] = '';
			}

			$validatorPassword = new Zend_Validate();
			$validatorPassword->addValidator(new Zend_Validate_StringLength(
												$this->scope->validate->password->lengthMin, 
												$this->scope->validate->password->lengthMax
											));
			if ($validatorPassword->isValid($password))
			{
				$login['password'] = $password;
			}
			else
			{
				$error['password'] = $this->scope->errorMessage->invalidPassword;
				$login['password'] = '';
			}			
		}
		return array('login'=>$login, 'error'=>$error);
	}
	
	/**
	 * Validate user input, add or update form
	 * @access public
	 * @param public 
	 * @return array
	 */
	public function validateUser($values)
	{
		
		$data = array();
		$error = array();
		//validate the input data	
		$validatorChain = new Zend_Validate();
		// Only validate the details parameters. Username, password and email will be also filtered
		$validDetails = Dot_Kernel::validateFilter($validatorChain, $values['details']);
		//validate email
		$validatorEmail = new Zend_Validate_EmailAddress();		
		$validEmail = Dot_Kernel::validateFilter($validatorEmail, $values['email']);
		$data = array_merge($data, $validDetails['data'], $validEmail['data']);
		$error = array_merge($error, $validDetails['error'], $validEmail['error']);
		//validate paswword				
		if($values['password']['password'] == $values['password']['password2'])
		{
			unset($values['password']['password2']);
			$validatorChain = new Zend_Validate();
			$validatorChain->addValidator(new Zend_Validate_StringLength(
											   $this->scope->validate->password->lengthMin, 
											   $this->scope->validate->password->lengthMax
										  ));
			$validPass = Dot_Kernel::validateFilter($validatorChain, $values['password']);
			$data = array_merge($data, $validPass['data']);
			$error = array_merge($error, $validPass['error']);	
		}
		else
		{
			$error['password'] = $this->scope->errorMessage->passwordTwice;
		}
		return array('data' => $data, 
					'error' => $error);
	}	
	/**
	 * Validate email user input
	 * @access public
	 * @param public 
	 * @return array
	 */
	public function validateEmail($value)
	{
		$data = array();
		$error = array();
		$validatorEmail = new Zend_Validate_EmailAddress();		
		$validEmail = Dot_Kernel::validateFilter($validatorEmail, array('email'=>$value));
		return $validEmail;
	}
	/**
	 * Send forgot password to user
	 * @acess public
	 * @param string $email
	 * @return array
	 */
	public function forgotPassword($email)
	{
		
		$select = $this->db->select()
						   ->from('user', array('password'))
						   ->where('email = ?',$email);
		$value = $this->db->fetchRow($select);
		if(!empty($value))
		{
			$dotEmail = new Dot_Email();
			$dotEmail->addTo($email);
			$dotEmail->setSubject('Forgot Password');			
			$dotEmail->setBodyText('Your password is '.$value['password']);			
			$succeed = $dotEmail->send();
			if($succeed)
			{
				$error['Email Sent'] = $this->scope->errorMessage->emailSent.$email;
			}
			else
			{
				$error['Email Not Sent'] = $this->scope->errorMessage->emailNotSent.$email;
			}		
		}
		else
		{
			$error['Not Found'] = $email.$this->scope->errorMessage->emailNotFound;
		}
		return $error;		
	}	
}