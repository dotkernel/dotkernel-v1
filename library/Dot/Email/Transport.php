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
	 * @param object $config
	 * @param string $to [optional]
	 * @param string $fromName [optional]
	 * @param string $fromEmail [optional]
	 * @param string $subject [optional]
	 * @return void
	 */
	public function __construct($to = null, $fromName = null, $fromEmail = null, $subject = null)
	{
		$this->db = Zend_Registry::get('database');
		$this->settings = Zend_Registry::get('settings');
		$this->to = $to;		
		$this->subject = $subject;
		$this->fromName = $fromName;
		$this->fromEmail = $fromEmail;
		parent::addHeader('X-Mailer', $this->xmailer);
		parent::addTo($this->to);
		parent::setSubject($this->subject);
		//  set the transporter
		//  check if we can use regular server sendmail
		$partial = @explode('@', $this->to);
		$this->smtp_data = $this->GetSMTP();		
		if(stristr($this->settings->smtp_addresses, $partial['1']) !== FALSE)
		{
			//  SMTP Transporter
			$mailConfigs = array('auth' => 'login', 
								 'username' => $this->settings->smtp_username, 
								 'password' => $this->settings->smtp_password, 
								 'ssl' => 'tls');
			$tr = new Zend_Mail_Transport_Smtp($this->settings->smtp_server, $mailConfigs);
			parent::setDefaultTransport($tr);
			parent::setFrom($this->settings->smtp_username, $this->fromName);
		}
		else
		{
			//  Sendmail transporter
			$tr = new Zend_Mail_Transport_Sendmail('-f'.$this->fromEmail);
			parent::setDefaultTransport($tr);
			parent::setFrom($this->fromEmail, $this->fromName);
		}
		
	}
	/**
	 * Send email
	 * @access public 
	 * @return void
	 */
	public function send()
	{	
		if(array_key_exists('id', $this->smtp_data))
		{	
			$this->UpdateSMTPCounter($this->smtp_data['id']);
		}
		parent::send();
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
		->from('email_transporter',array('id','smtp_username'=>'user','smtp_password'=>'pass'))
		->where('counter < limit_number')
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