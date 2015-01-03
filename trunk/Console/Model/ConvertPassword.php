<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    CLI
 * @subpackage User
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 * @author     DotKernel Team <team@dotkernel.com>
 */

/**
 * Convert password from table user to new Password API format 
 * @author DotKernel Team <team@dotkernel.com>
 *
 */

class Console_Model_ConvertPassword extends Dot_Model_User
{
	
	/**
	 * Constructor
	 * @access public 
	 * @return Dot_Model_User
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getUsers($limit)
	{
		$result = $this->db->fetchPairs("SELECT id, password FROM user WHERE passwordNew='' LIMIT " . (int)$limit );
		return $result;
	}
	
	/**
	 * Convert the password of a user , update the database
	 * @param integer $id
	 * @param string  $oldPassword
	 * @return bool
	 */
	public function convertPasswordUser($id, $oldPassword)
	{
		$newPassword = $this->passwordApi->hashPassword($oldPassword, PASSWORD_DEFAULT);
		$affectedRows = $this->db->update('user', array('passwordNew' => $newPassword), 'id = '.$id);
		return $affectedRows;
	}
}