<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* User Model
* Here are all the actions related to the user
* @category   DotKernel
* @package    Admin 
* @author     DotKernel Team <team@dotkernel.com>
*/
class User
{
	/**
	 * Constructor
	 * @access public
	 */
	public function __construct()
	{
		$this->db = Zend_Registry::get('database');				
		$this->option = Zend_Registry::get('option');	
		$this->settings = Zend_Registry::get('settings');			
		$seo = new Dot_Seo();
		$this->seoOption = $seo->getOption();	
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
	 * Get user list
	 * @access public 
	 * @param int $page [optional]
	 * @return array(array(), Zend_Paginator_Adapter())
	 */
	public function getUserList($page = 1)
	{
		$select = $this->db->select()->from('user');
				
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
	 * Delete user
	 * @param int $id
	 * @return void
	 */
	public function deleteUser($id)
	{
		$this->db->delete('user', 'id = ' . $id);
	}	
	/**
	 * Send forgot password to user
	 * @acess public
	 * @param string $email
	 * @return void
	 */
	public function sendPassword($id)
	{
		$session = Zend_Registry::get('session');
		$value = $this->getUserBy('id', $id);
		if(!empty($value))
		{
			$dotEmail = new Dot_Email();
			$dotEmail->addTo($value['email']);
			$dotEmail->setSubject($this->seoOption->siteName . ' - ' . $this->option->forgotPassword->subject);
			$msg = str_replace(array('%FIRSTNAME%', '%PASSWORD%'), 
							   array($value['firstName'], $value['password']), 
				              $this->option->forgotPassword->message);
			$dotEmail->setBodyText($msg);		
			$succeed = $dotEmail->send();
			if($succeed)
			{
				$session->message['txt'] = $this->option->infoMessage->emailSent.$value['email'];
				$session->message['type'] = 'info';
			}
			else
			{
				$session->message['txt'] = $this->option->errorMessage->emailNotSent.$value['email'];
				$session->message['type'] = 'error';
			}		
		}
		else
		{
			$session->message['txt'] = $value['email'].$this->option->infoMessage->emailNotFound;
			$session->message['type'] = 'info';
		}		
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
	 * Activate/Inactivate user account
	 * @param int $id - user ID
	 * @param int $isActive
	 * @return void
	 */
	public function activateUser($id, $isActive)
	{		
        $this->db->update('user', array('isActive' => $isActive), 'id = '.$id);
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
					   ->from('userLogin');
		if ($id > 0) 
		{
			$select->where('userId = ?', $id);
		}
		$select->order('dateLogin DESC');
 		$paginatorAdapter = new Zend_Paginator_Adapter_DbSelect($select);
		($page == 1) ? 
			$select->limit($this->settings->resultsPerPage) : 
			$select->limit($this->settings->resultsPerPage, ($page-1)*$this->settings->resultsPerPage);
							
		$data = $this->db->fetchAll($select);
		return array('data'=> $data,'paginatorAdapter'=> $paginatorAdapter);
	}	
}
