<?php
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
*
* @category   DotKernel
* @package    Admin
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
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
	public function getAdminBy($field = '', $value = '')
	{		
		$select = $this->db->select()
					   ->from('admin')
					   ->where($field.' = ?', $value)
					   ->limit(1);					   
		$result = $this->db->fetchRow($select);
		return $result;
	}	
	/**
	 * Get admin info
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getAdminInfo($id)
	{
		$select = $this->db->select()
						   ->from('admin')
						   ->where('id = ?', $id);
		return $this->db->fetchRow($select);
	}
	/**
	 * Get user list
	 * @access public 
	 * @param int $page [optional]
	 * @return array(array(), Zend_Paginator_Adapter())
	 */
	public function getUserList($page = 1)
	{
		$select = $this->db->select()->from('admin');
				
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
		// if you want to add an inactive user, uncomment the below line
		// $data['isActive'] = 0;
		$data['password'] = md5($data['username'].$this->config->settings->admin->salt.$data['password']);
		$this->db->insert('admin',$data);		
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
			$user = $this->getAdminInfo($id);
			$data['password'] = md5($user['username'].$this->config->settings->admin->salt.$data['password']);
		}
        $this->db->update('admin', $data, 'id = '.$id);
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
		//validate the input data - Username, password and email will be also filtered
		//only validate the details parameters	
		$validatorChain = new Zend_Validate();
		$validDetails = Dot_Kernel::validateFilter($validatorChain, $values['details']);
		//validate username
		if(array_key_exists('username', $values))
		{
			$validatorChain = new Zend_Validate();
			$validatorChain->addValidator(new Zend_Validate_Alpha())
							->addValidator(new Zend_Validate_StringLength(
													$this->scope->validate->details->lengthMin, 
													$this->scope->validate->details->lengthMax
												));
			$validUsername = Dot_Kernel::validateFilter($validatorChain, $values['username']);
			$data = array_merge($data, $validUsername['data'], $validDetails['data']);
			$error = array_merge($error, $validUsername['error'], $validDetails['error']);
		}
		//validate email
		$validatorEmail = new Zend_Validate_EmailAddress();		
		$validEmail = Dot_Kernel::validateFilter($validatorEmail, $values['email']);
		$data = array_merge($data, $validEmail['data']);
		$error = array_merge($error, $validEmail['error']);
		//validate password				
		if($values['password']['password'] != '' || $values['password']['password2'] != '')
		{			
			if($values['password']['password'] == $values['password']['password2'])
			{
				unset($values['password']['password2']);
				$validatorChain = new Zend_Validate();
				$validatorChain->addValidator(new Zend_Validate_Alnum())
							   ->addValidator(new Zend_Validate_StringLength(
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
		}
		return array('data' => $data, 'error' => $error);
	}
}