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
		$this->smtpData = $this->getSMTP();		
		$mailConfigs = array('auth' => 'login',
                             'username' => $this->smtpData['smtpUsername'],
                             'password' => $this->smtpData['smtpPassword'],
                             'ssl' => 'tls');
		$this->transport = new Zend_Mail_Transport_Smtp($this->smtpData['smtpServer'], $mailConfigs);		
	}	
	/**
	 * Return the transporter
	 * @access public
	 * @return Zend_Mail_Transport_Smtp
	 */
	public function getTransport()
	{
		$this->updateSMTPCounter($this->smtpData['id']);
		return $this->transport;
	}
	/**
	 * Get the current SMTP for sending the emails
	 * @access private
	 * @return array
	 */
	private function getSMTP ()
	{
		$smtp = array();
		$select = $this->db->select()
						   ->from('emailTransporter',array('id', 'smtpUsername' => 'user', 'smtpPassword' => 'pass', 'smtpServer' => 'server'))
						   ->where('counter < capacity')
						   ->where('isActive = ?','1')
						   ->order('id')
						   ->limit('1');
		$result = $this->db->fetchAll($select);	
		if (count($result) > 0)
		{			
			$smtp = $result[0];
			
		}else
		{
			
			$where = array(" `date` < DATE_FORMAT( NOW( ) , '%Y-%m-%d' )","isActive = '1'");
			$this->db->update('emailTransporter', array('counter'=>0), $where);
			$this->db->update('emailTransporter', array('date'=>'NOW()'), $where);
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
	private function updateSMTPCounter ($id)
	{
		$this->db->query("UPDATE emailTransporter SET counter = counter+1 WHERE id = '".$id."'");
	}
}