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
* Alternate SMTP and default server mail() class 
* @category   DotKernel
* @package    DotLibrary
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
	public function __construct($config, $to = null, $fromName = null, $fromEmail = null, $subject = null)
	{
		$this->settings = $config;
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
			$mailConfigs = array('auth' => 'login', 'username' => $this->settings->smtp_username, 'password' => $this->settings->smtp_password, 'ssl' => 'tls');

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
		/**
		 * @TODO is this the proper error trapping system 		 
		 */
		try
		{
			$this->UpdateSMTPCounter($this->smtp_data['id']);
			parent::send();
		}
		catch (Zend_Exception $e)
		{
			/**
			 * @TODO definitely we want to create an exception class, Other code to recover from the error
			 */
			$dev_emails = @explode(',', $this->settings->dev_emails);
			$date_now = date('F dS, Y h:i:s A');
			$mailSubject  = "SMTP Error on ". $this->settings->site_name;
			$mailContent  = "We were unable to send SMTP email."."\n";
			$mailContent .= "---------------------------------"."\n";
			$mailContent .="Caught exception: ". get_class($e)."\n";
			$mailContent .="Message:  ".$e->getMessage()."\n";
			$mailContent .= "---------------------------------"."\n\n";
			$mailContent .="To Email: ".$this->To."\n";
			$mailContent .="From Email: ".$this->FromEmail."\n";
			$mailContent .="Date: ".$date_now ."\n";
			$mailHeader   = "From: ".$this->settings->contact_recipient."\r\n";
			$mailHeader  .= "Reply-To:".$this->config->contact_recipient."\r\n"."X-Mailer: PHP/".phpversion();
			foreach($dev_emails as $ky => $mailTo)
			{
				mail($mailTo, $mailSubject, $mailContent, $mailHeader);
			}
		}
	}
	function GetSMTP ()
	{
		$smtp = array();
		$this->db->query("SELECT id, user, pass FROM newsletter_smtp WHERE `date` = DATE_FORMAT( NOW( ) , '%Y-%m-%d' ) AND counter < '2000' AND active = 'Y' ORDER BY id ASC LIMIT 1");
		if ($this->db->num_rows() == 1)
		{
			$this->db->next_record();
			$smtp['id'] = $this->db->f('id');
			$smtp['smtp_username'] = $this->db->f('user');
			$smtp['smtp_password'] = $this->db->f('pass');
		}else
		{
			$this->db->query("UPDATE newsletter_smtp SET counter = 0 WHERE `date` < DATE_FORMAT( NOW( ) , '%Y-%m-%d' ) AND active = 'Y'");
			$this->db->query("UPDATE newsletter_smtp SET date = NOW() WHERE `date` < DATE_FORMAT( NOW( ) , '%Y-%m-%d' ) AND active = 'Y'");
			$this->db->query("SELECT id, user, pass FROM newsletter_smtp WHERE `date` = DATE_FORMAT( NOW( ) , '%Y-%m-%d' ) AND counter < '2000' AND active = 'Y' ORDER BY id ASC LIMIT 1");
			if ($this->db->num_rows() == 1)
			{
				$this->db->next_record();
				$smtp['id'] = $this->db->f('id');
				$smtp['smtp_username'] = $this->db->f('user');
				$smtp['smtp_password'] = $this->db->f('pass');
			}
		}
		return $smtp;
	}

	function UpdateSMTPCounter ($id)
	{
		$this->db->query("UPDATE newsletter_smtp SET counter = counter+1 WHERE id = '".$id."'");
	}
}