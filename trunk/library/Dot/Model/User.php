<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Use Model Class 
* @category   DotKernel
* @package    DotLibrary
* @subpackage DotModel
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Model_User extends Dot_Model
{
	/**
	 * Email constructor
	 * @access public 
	 * @return Dot_Model_User
	 */
	public function __construct()
	{
		parent::__construct();
		$this->option = Zend_Registry::get('option');
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
		$this->db->insert('user',$data);
	}
}