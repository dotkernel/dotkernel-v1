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
* Admin User Model
* Here are all the actions related to the user
* @category   DotKernel
* @package    Admin 
* @author     DotKernel Team <team@dotkernel.com>
*/
class System
{
	/**
	 * Constructor
	 * @access public
	 */
	public function __construct()
	{
		$this->db = Zend_Registry::get('database');
	}
	public function listSettings($editable='1')
	{
		$select = $this->db->select()
						   ->from('settings')
						   ->where('editable = ? ', $editable);
		return $this->db->fetchAll($select);
	}
	public function updateSettings($data)
	{
		Zend_Debug::dump($data);	
		foreach ($data as $k => $v)
		{			
			$this->db->update('settings', array('value' => $v), 'variable = '.$this->db->quote($k));
		}	
		
	}
}
