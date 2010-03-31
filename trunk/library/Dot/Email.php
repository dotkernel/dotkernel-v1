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
* @subpackage DotEmail
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Email extends Zend_Mail
{
	/**
	 * Mailer
	 * @access public
	 * @var string
	 */
	public $xmailer = 'DotKernel Mailer';
	/**
	 * Email constructor
	 * @access public 
	 * @return void
	 */
	public function __construct()
	{
		$this->settings = Zend_Registry::get('settings');
		$this->db = Zend_Registry::get('database');
		$this->settings = Zend_Registry::get('settings');
		$this->addHeader('X-Mailer', $this->xmailer);		
	}
	/**
	 * Set content
	 * @access public 
	 * @param string $content
	 * @param string $format [optional]
	 * @return void
	 */
	public function setContent ($content, $format = 'text/plain')
	{
		if ($format == 'text/html' )
		{
			parent::setBodyHtml($content);
		}
		else
		{
			parent::setBodyText($content);
		}
	}
	
	/**
	 * Send email. Parameter is included only to be compatible with Zend_Mail
	 * @access public 
	 * @param  Zend_Mail_Transport_Abstract $transport [optional]
	 * @return void
	 */
	public function send($transport = null)
	{		
		//check id replyTo is empty
		if(is_null($this->getReplyTo()))	
		{
			$this->setReplyTo($this->settings->contact_recipient, $this->settings->site_name);
		}
		//don't check if returnPath is empty, because if not set, will return $this->_from
		//  set the transporter		
		if('1' == $this->settings->smtp_use)
		{
			$partial = @explode('@', $this->_to[0]);
			if(stristr($this->settings->smtpAddresses, $partial['1']) !== FALSE)
			{
				//  SMTP Transporter
				$tr = new Dot_Email_Transport();
			}
		}
		else
		{
			$tr = new Dot_Email_Simple($this->_from);			
		}
		$this->setDefaultTransport($tr->getTransport());
		/**
		 * @TODO is this the proper error trapping system 		 
		 */
		try
		{
			parent::send();
			return TRUE;
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
			$to = $this->getRecipients();
			$mailContent .="To Email: ".$to[0]."\n";
			$mailContent .="From Email: ".$this->getFrom()."\n";
			$mailContent .="Date: ".$date_now ."\n";
			$mailHeader   = "From: ".$this->settings->contact_recipient."\r\n";
			$mailHeader  .= "Reply-To:".$this->settings->contact_recipient."\r\n"."X-Mailer: PHP/".phpversion();
			foreach($dev_emails as $ky => $mailTo)
			{
				var_dump($mailTo, $mailSubject, $mailContent, $mailHeader);
				mail($mailTo, $mailSubject, $mailContent, $mailHeader);
			}
			return FALSE;
		}
	}
}