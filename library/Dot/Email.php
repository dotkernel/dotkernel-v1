<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotLibrary
 * @copyright  Copyright (c) 2009-2012 DotBoost Technologies Inc. (http://www.dotboost.com)
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
	 * @return Zend_Mail
	 */
	public function __construct()
	{
		$this->settings = Zend_Registry::get('settings');
		$this->db = Zend_Registry::get('database');
		$this->addHeader('X-Mailer', $this->xmailer);
		$this->seoOption = Zend_Registry::get('seo');
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
	 * @return bool
	 */
	public function send($transport = null)
	{		
		// set From and ReplyTo, in case we forgot it in code
		parent::setDefaultFrom($this->settings->siteEmail, $this->seoOption->siteName);
		parent::setDefaultReplyTo($this->settings->siteEmail, $this->seoOption->siteName);
		//  set the sendmail transporter as default 
		$tr = new Dot_Email_Sendmail($this->_from);			
		// check if we need to use an external SMTP
		if('1' == $this->settings->smtpActive)
		{
			$partial = @explode('@', $this->_to[0]);
			if(stristr($this->settings->smtpAddresses, $partial['1']) !== FALSE)
			{
				$tr = new Dot_Email_Smtp();
				if(empty($tr->smtpData))
				{
					// we can't use SMTP in this case 
					$tr = new Dot_Email_Sendmail($this->_from);	
				}
			}
		}
		$this->setDefaultTransport($tr->getTransport());		
		//try to send the email
		try
		{
			parent::send();
			return TRUE;
		}
		catch (Zend_Exception $e)
		{
			/**
			 * @todo definitely we want to create an exception class, Other code to recover from the error
			 */
			$devEmails = @explode(',', $this->settings->devEmails);
			$dateNow = date('F dS, Y h:i:s A');
			$mailSubject  = "SMTP Error on ". $this->seoOption->siteName;
			$mailContent  = "We were unable to send SMTP email."."\n";
			$mailContent .= "---------------------------------"."\n";
			$mailContent .="Caught exception: ". get_class($e)."\n";
			$mailContent .="Message:  ".$e->getMessage()."\n";
			$mailContent .= "---------------------------------"."\n\n";
			$to = $this->getRecipients();
			$mailContent .="To Email: ".$to[0]."\n";
			$mailContent .="From Email: ".$this->getFrom()."\n";
			$mailContent .="Date: ".$dateNow ."\n";
			$mailHeader   = "From: ".$this->settings->siteEmail."\r\n";
			$mailHeader  .= "Reply-To:".$this->settings->siteEmail."\r\n"."X-Mailer: PHP/".phpversion();
			foreach($devEmails as $ky => $mailTo)
			{
				mail($mailTo, $mailSubject, $mailContent, $mailHeader);
			}
			return FALSE;
		}
	}
}