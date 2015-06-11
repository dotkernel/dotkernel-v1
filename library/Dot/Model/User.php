<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * User Model Class 
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotModel
 * @see		  Dot_Model
 * @author     DotKernel Team <team@dotkernel.com>
 */

class Dot_Model_User extends Dot_Model
{

	/**
	 * Constructor
	 * @access public 
	 * @return Dot_Model_User
	 */
	public function __construct()
	{
		parent::__construct();
		$this->option = Zend_Registry::get('option');
		$this->passwordApi = new Dot_Password();
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
	 * Update user
	 * @access public
	 * @param array $data
	 * @return void
	 */
	public function updateUser($data)
	{
		$id = $data['id'];
		unset ($data['id']);
		
		//Update password only if is set a new password 
		if(array_key_exists('password', $data))
		{
			$data['password'] = $this->passwordApi->hashPassword($data['password'], PASSWORD_DEFAULT);
		}

		$this->db->update('user', $data, 'id = '.$id);
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
		$data['password'] = $this->passwordApi->hashPassword($data['password'], PASSWORD_DEFAULT);
		$this->db->insert('user',$data);
	}
}