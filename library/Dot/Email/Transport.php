<?php
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Alternate SMTP server mail() class 
* @category   DotKernel
* @package    DotLibrary
* @subpackage DotEmail
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Email_Transport extends Dot_Email
{
	/**
	 * Email constructor
	 * @access public 
	 * @param string $to [optional]
	 * @return void
	 */
	public function __construct($to = null, $fromName = null, $fromEmail = null, $subject = null)
	{
		$this->db = Zend_Registry::get('database');
		$this->smtp_data = $this->GetSMTP();		
		$mailConfigs = array('auth' => 'login',
                     'username' => $this->smtp_data['smtp_username'],
                     'password' => $this->smtp_data['smtp_password'],
                     'ssl' => 'tls');
		$this->transport = new Zend_Mail_Transport_Smtp($this->smtp_data['smtp_server'], $mailConfigs);		
	}	
	/**
	 * Return the transporter
	 * @access public
	 * @return Zend_Mail_Transport_Smtp
	 */
	public function getTransport()
	{
		$this->UpdateSMTPCounter($this->smtp_data['id']);
		return $this->transport;
	}
	/**
	 * Get the curent SMTP for sending the emails
	 * @access private
	 * @return array
	 */
	private function GetSMTP ()
	{
		$smtp = array();
		$select = $this->db->select()
		->from('email_transporter',array('id','smtp_username'=>'user','smtp_password'=>'pass','smtp_server'=>'server'))
		->where('counter < capacity')
		->where('active = ?','Y')
		->order('id')
		->limit('1');
		$result = $this->db->fetchAll($select);	
		if (count($result) > 0)
		{			
			$smtp = $result[0];
			
		}else
		{
			
			$where = array(" `date` < DATE_FORMAT( NOW( ) , '%Y-%m-%d' )","active = 'Y'");
			$this->db->update('email_transporter', array('counter'=>0), $where);
			$this->db->update('email_transporter', array('date'=>'NOW()'), $where);
			$select->where("`date` = DATE_FORMAT( NOW( ) , '%Y-%m-%d' )");
			$result = $this->db->fetchAll($select);	
			if (count($result) > 0)
			{			
				$smtp = $result[0];
				
			}
		}
		return $smtp;
	}
	/**
	 * Update the counter of the current transporter.
	 * @access private
	 * @param int $id
	 * @return void
	 */
	private function UpdateSMTPCounter ($id)
	{
		$this->db->query("UPDATE email_transporter SET counter = counter+1 WHERE id = '".$id."'");
	}
}