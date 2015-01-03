<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotLibrary
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

 /**
 * Validate User
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotValidate
 * @see		   Dot_Validate
 * @author     DotKernel Team <team@dotkernel.com>
 */

class Dot_Validate_User extends Dot_Validate
{
	/**
	 * Validate user options
	 * Is an array with the following keys
	 * 			- who: string - for which type of user the validation is made (user, admin, ...)
	 * 			- action: string - from which action is called the validation(login, add, update, activate, ...)
	 * 			- values: array - what should validate
	 * 			- userId: integer - used for checking the uniqueness of the user(by username or email)
	 * @var array
	 * @access private
	 */
	private $_options = array('who' => 'user',
														'action' => '',
														'values' => array(),
														'userId' => 0);
	/**
	 * Valid data after validation
	 * @var array
	 * @access private
	 */
	private $_data = array();
	/**
	 * Errors found on validation
	 * @var array
	 * @access private
	 */
	private $_error = array();
	/**
	 * Constructor
	 * @access public
	 * @param array $options [optional]
	 * @return Dot_Validate
	 */
	public function __construct($options = array())
	{
		$this->option = Zend_Registry::get('option');
		foreach ($options as $key =>$value)
		{
			$this->_options[$key] = $value;
		}
	}
	/**
	 * Check if data is valid
	 * @access public
	 * @return bool
	 */
	public function isValid()
	{
		$this->_data = array();
		$this->_error = array();
		$values = $this->_options['values'];
		//validate the input data - username, password and email will be also filtered
		$validatorChain = new Zend_Validate();
		$dotFilter = new Dot_Filter();
		//validate details parameters
		if(array_key_exists('details', $values))
		{
			$validatorChain->addValidator(new Zend_Validate_StringLength(array('min' => $this->option->validate->details->lengthMin)));
			$this->_callFilter($validatorChain, $values['details']);
		}
		//validate username
		if(array_key_exists('username', $values))
		{
			$validatorChain = new Zend_Validate();
			$validatorChain->addValidator(new Zend_Validate_Alnum())
											->addValidator(new Zend_Validate_StringLength($this->option->validate->details->lengthMin,
																																		$this->option->validate->details->lengthMax));
			$this->_callFilter($validatorChain, $values['username']);
			if(in_array($this->_options['action'], array('add', 'update')))
			{
				$uniqueError = $this->_validateUnique('username', $values['username']['username']);
				$this->_error = array_merge($this->_error, $uniqueError);
			}
		}
		//validate email
		if(array_key_exists('email', $values))
		{
			$validatorEmail = new Zend_Validate_EmailAddress();
			$this->_callFilter($validatorEmail, $values['email']);
			if(in_array($this->_options['action'], array('add', 'update')))
			{
				$uniqueError = $this->_validateUnique('email', $values['email']['email']);
				$this->_error = array_merge($this->_error, $uniqueError);
			}
		}
		//validate enum
		if(array_key_exists('enum', $values))
		{
			$validatorEnum = new Zend_Validate_InArray(explode(',', $values['enum'][0]));
			unset($values['enum'][0]);
			$this->_callFilter($validatorEnum, $values['enum']);
		}
		//validate phone
		if(array_key_exists('phone', $values))
		{
			//filter, then valid, so we wont use _callFilter() method
			$phoneOptions = array('countryCode' => 'US');
			$phoneOptions['values'] = $values['phone']['phone'];
			$dotValidatorPhone = new Dot_Validate_Phone($phoneOptions);
			if($dotValidatorPhone->isValid())
			{
				$this->_data = array_merge($this->_data, array('phone' => $dotValidatorPhone->getData()));
			}
			else
			{
				$this->_error = array_merge($this->_error, array('phone' => $dotValidatorPhone->getError()));
			}
		}
		//validate password
		if(array_key_exists('password', $values) && isset($values['password']['password']))
		{
			if(isset($values['password']['password2'])
				&& $values['password']['password'] != $values['password']['password2'])
			{
				$this->_error['password'] = $this->option->errorMessage->passwordTwice;
			}
			else
			{
				if(isset($values['password']['password2']))
					unset($values['password']['password2']);
				$validatorChain = new Zend_Validate();
				$validatorChain->addValidator(new Zend_Validate_StringLength(
												$this->option->validate->password->lengthMin,
												$this->option->validate->password->lengthMax
											));
				$this->_callFilter($validatorChain, $values['password']);
			}
		}
		// validate captcha
		if(array_key_exists('captcha', $values))
		{
			if(!array_key_exists('recaptcha_response_field', $values['captcha'])
					 || strlen($values['captcha']['recaptcha_response_field']) == 0)
			{
				$this->_error = array_merge($this->_error, array('Secure Image' => $this->option->errorMessage->captcha));
			}
			else
			{
				// validate secure image code
				try
				{
					// just in frontend is recaptcha included.
					// if you want it in other modules, add getRecaptcha() method from frontend/View.php in others View.php of the modules
					$view = View::getInstance();
					$result = $view->getRecaptcha()->verify($values['captcha']['recaptcha_challenge_field'],
																										$values['captcha']['recaptcha_response_field']);
					if (!$result->isValid())
					{
						$this->_error = array_merge($this->_error, array('Secure Image' => $this->option->errorMessage->captcha));
					}
				}
				catch(Zend_Exception $e)
				{
					$this->_error = array_merge($this->_error, array('Secure Image' => $this->option->errorMessage->captcha. ' '
																			. $e->getMessage()));
				}
			}
		}
		if(empty($this->_error))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	/**
	 * Get valid data
	 * @access public
	 * @return array
	 */
	public function getData()
	{
		return $this->_data;
	}
	/**
	 * Get errors encounter on validation
	 * @access public
	 * @return array
	 */
	public function getError()
	{
		return $this->_error;
	}
	/**
	 * Check if user already exists - email, username, and return error
	 * @access private
	 * @param string $field
	 * @param string $value
	 * @return array
	 */
	private function _validateUnique($field, $value)
	{
		$error = array();
		//email is unique, check if exists
		$exists = $this->_getUserBy($field, $value);
		if($this->_options['userId'] > 0)
		{
			$currentUser = $this->_getUserBy('id', $this->_options['userId']);
			$uniqueCondition = (is_array($exists) && $exists[$field] != $currentUser[$field]);
		}
		else
		{
			$uniqueCondition = (FALSE != $exists);
		}
		if($uniqueCondition)
		{
			$error[$field] = $value . $this->option->errorMessage->userExists;
		}
		return $error;
	}
	/**
	 * Get admin by field
	 * @access public
	 * @param string $field
	 * @param string $value
	 * @return array
	 */
	public function _getUserBy($field = '', $value = '')
	{
		$db = Zend_Registry::get('database');
		$select = $db->select()
									->from($this->_options['who'])
									->where($field.' = ?', $value)
									->limit(1);
		$result = $db->fetchRow($select);
		return $result;
	}
	/**
	 * Call filter method from DotFilter
	 * @access private
	 * @param Zend_Validate $validator
	 * @param array $values
	 * @return void
	 */
	private function _callFilter($validator, $values)
	{
		$dotFilter = new Dot_Filter(array('validator' => $validator, 'values' => $values));
		$dotFilter->filter();
		$this->_data = array_merge($this->_data, $dotFilter->getData());
		$this->_error = array_merge($this->_error, $dotFilter->getError());
	}
}
