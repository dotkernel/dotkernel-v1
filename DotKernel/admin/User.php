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
	 * @return array	 
	 */
	public function getUserList($page = 1)
	{
		$select = $this->db->select()
						   ->from('user');				
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
		$this->db->insert('user', $data);		
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
	 * @access public
	 * @param int id
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
	 * $values is an array on multiple levels. On first level, the key suggest what validation will be done.
	 * - details 	- only filter the input
	 * - username	- validate with Zend_Validate_Alnum, Zend_Validate_StringLength and filter the input
	 * - email		- validate with Zend_Validate_EmailAddress and filter the input 
	 * - enum 		- validate with Zend_Validate_InArray(explode(',', $values['enum'][0])) and filter the input
	 * - password	- validate with Zend_Validate_StringLength and filter the input
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
			$validatorChain->addValidator(new Zend_Validate_Alnum())
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
		if(array_key_exists('password', $values) && ($values['password']['password'] != '' || $values['password']['password2'] != ''))
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
	 * @param string $browser [optional]
	 * @return array
	 */
	public function getLogins($id, $page = 1, $browser = '', $loginDate = '', $sortField = '', $orderBy = '')
	{
		$select = $this->db->select()
						->from('userLogin')
						->joinLeft(
							'user',
							'userLogin.userId = user.id',
							'username'
						);
		if ($id > 0) 
		{
			$select->where('userId = ?', $id);
		}
		if ($browser != '')
		{
			$select->where($this->db->quoteInto("userAgent LIKE ? ", '%'.$browser.'%'));
		}
		if ($loginDate != '')
		{
			$select->where('dateLogin LIKE ?', '%'.$loginDate.'%');
		}
		if ($sortField!=""){
			$select->order($sortField. ' '.$orderBy);
		}		
		$dotPaginator = new Dot_Paginator($select, $page, $this->settings->resultsPerPage);
		return $dotPaginator->getData();
		
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
	
	/**
	 * Get top country user logins as declared in 
	 * $option->countCountryUserLogin
	 * @access public
	 * @return array
	 */
	public function getTopCountryLogins($topCount)
	{
		$select = $this->db->select()
					  	   ->from('userLogin');
		$logins = $this->db->fetchAll($select);
		$countryName = array();
		$countryCount = array();
		foreach ($logins as $v)
		{
			if(array_key_exists($v['country'], $countryCount))
			{
				$countryCount[$v['country']]++;
			}
			else
			{
				 $countryCount[$v['country']] = 1;
			}
		}
		arsort($countryCount);
		$countSum = array_sum($countryCount);
		$i = 1;
		$data['Other'] = array('count' => 0, 'countPercent' => 0,'name' => 'Others');
		foreach ($countryCount as $country => $count)
		{
			$countPercent = round($count * 100 / $countSum, 2);
			if($i >= $topCount)
			{
				$data['Other']['countPercent'] += $countPercent; 
				$data['Other']['count'] += $count; 
			}
			else
			{
				$data[$country]['countPercent'] = $countPercent; 
				$data[$country]['count'] = $count; 
				
			}
			$i++;
		}
		if(!$data['Other']['count'])
		{
			unset($data['other']);
		}
		return $data;
	}
}
