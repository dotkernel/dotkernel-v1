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
* System Model
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
	public function listSettings($isEditable='1')
	{
		$select = $this->db->select()
						   ->from('setting')
						   ->where('isEditable = ? ', $isEditable);
		return $this->db->fetchAll($select);
	}
	public function updateSettings($data)
	{		
		foreach ($data as $k => $v)
		{			
			$this->db->update('setting', array('value' => $v), $this->db->quoteIdentifier('key').' = '.$this->db->quote($k));
			
		}	
		
	}
}
