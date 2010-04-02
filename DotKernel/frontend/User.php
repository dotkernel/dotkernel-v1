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
		$validatorEmail = new Zend_Validate_EmailAddress();		
		$validEmail = Dot_Kernel::validate($validatorEmail, array('email'=>$value));
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
		
		$select = $this->db->select()->from('user', array('password'))->where('email = ?',$email);
		$value = $this->db->fetchRow($select);
		if(!empty($value))
		{
			$dotEmail = new Dot_Email();
			$dotEmail->addTo($email);
			$dotEmail->setFrom($this->settings->contact_recipient, $this->settings->site_name);
			$dotEmail->setSubject('Forgot Password');			
			$dotEmail->setBodyText('Your password is '.$value['password']);
			
			$succeed = $dotEmail->send();
			if($succeed)
			{
				$error['Email Sent'] = 'Your password was sent to '.$email;
			}
			else
			{
				$error['Email Not Sent'] = 'Your password could not be sent to '.$email;
			}	
			echo "Message sent!";		
		}
		else
		{
			$error['Not Found'] = 'Email '.$email.' was not found in our records !';
		}
		return $error;
		
	}
	// and so on, functions related to USER 
}