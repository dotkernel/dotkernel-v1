<?php
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
*
* @category   DotKernel
* @package    Frontend
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* User Model
* Here are all the actions related to the user
* @category   DotKernel
* @package    Frontend
* @author     DotKernel Team <team@dotkernel.com>
*/

class Frontend_User
{
	/**
	 * Constructor
	 * @access public 
	 * @return Frontend_User
	 */
	public function __construct()
	{		
		$this->db = Zend_Registry::get('database');
		$this->config = Zend_Registry::get('configuration');
		$this->settings = Zend_Registry::get('settings');
		
	}
	/**
	 * Check to see if user can login
	 * @access public
	 * @param array $data
	 * @return array
	 */
	public function checkLogin($data)
	{
		$query = "SELECT * FROM user
								WHERE username = ? 
									AND password = ? 
										AND isActive = '1'";
		$stmt = $this->db->query($query,array($data['username'], $data['password']));
		$results = $stmt->fetchAll();
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
	 * Get user by field
	 * @access public
	 * @param string $field
	 * @param string $value
	 * @return array
	 */
	public function getUserBy($field = '', $value = '')
	{
		$query = "SELECT * FROM user
								WHERE $field = ? 
									LIMIT 1"; 
		$stmt = $this->db->query($query,array($value));
		$results = $stmt->fetchAll();
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
	 * Logout admin user. Using Dot_Authorize class
	 * @access public
	 * @return void
	 */
	public function logout()
	{
		Dot_Authorize::logout('user');
	}
	/**
	 * Get user info
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getUserInfo($id)
	{
		$query = "SELECT * FROM user WHERE id = ? ";
		$stmt = $this->db->query($query,$id);		
		return $stmt->fetch();
	}		
	/**
	 * Add new user
	 * @access public
	 * @param array $data
	 * @return void
	 */
	public function addUser($data)
	{		
		// if you want to add an inactive user, uncomment the below line
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
	 * Validate user input, add or update form
	 * @access public
	 * @param public 
	 * @return array
	 */
	public function validateUser($values)
	{
		
		$data = array();
		$error = array();
		//validate the imput data	
		$validatorChain = new Zend_Validate();
		$validatorChain->addValidator(new Zend_Validate_Alpha())
						->addValidator(new Zend_Validate_StringLength(3,20));
		$validAlpha = Dot_Kernel::validate($validatorChain, $values['alpha']);
		//validate email
		$validatorEmail = new Zend_Validate_EmailAddress();		
		$validEmail = Dot_Kernel::validate($validatorEmail, $values['email']);
		$data = array_merge($data, $validAlpha['data'], $validEmail['data']);
		$error = array_merge($error, $validAlpha['error'], $validEmail['error']);
		//validate paswword				
		if($values['password']['password'] == $values['password']['password2'])
		{
			unset($values['password']['password2']);
			$validatorChain = new Zend_Validate();
			$validatorChain->addValidator(new Zend_Validate_Alnum())
							->addValidator(new Zend_Validate_StringLength(3,20));
			$validPass = Dot_Kernel::validate($validatorChain, $values['password']);
			$data = array_merge($data, $validPass['data']);
			$error = array_merge($error, $validPass['error']);	
		}
		else
		{
			$error['password'] = "You didn't enter the same password twice. Please re-enter your password";
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
		//validate email
		$validatorEmail = new Zend_Validate_EmailAddress();		
		$validEmail = Dot_Kernel::validate($validatorEmail, array('email'=>$value));
		return $validEmail;
	}
	public function setUserInfo()
	{}
	public function forgotPassword($email)
	{
		
		$select = $this->db->select()->from('user', array('password'))->where('email = ?',$email);
		$value = $this->db->fetchRow($select);
		if(!empty($value))
		{
			$send = new Dot_Email($email, $this->settings->site_name, $this->settings->contact_recipient,'Forgot Password');
			$send->setTextContent('Your password is '.$value['password']);
			$succeed = $send->Send();
			if($succeed)
			{
				$error['Email Sent'] = 'Your password was sent to '.$email;
			}
			else
			{
				$error['Email Not Sent'] = 'Your password could not be sent to '.$email;
			}			
		}
		else
		{
			$error['Not Found'] = 'Email '.$email.' was not found in our records !';
		}
		return $error;
		
	}
	// and so on, functions related to USER 
}