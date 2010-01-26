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
	 * Settings
	 * @access private 
	 * @var object
	 */
	protected $settings = null;
	/**
	 * To email
	 * @access protected 
	 * @var string
	 */
	protected $to = null;
	/**
	 * Cc email
	 * @access protected 
	 * @var string
	 */
	protected $cc = null;
	/**
	 * Bcc email
	 * @access public 
	 * @var string
	 */
	public $bcc = null;
	/**
	 * From email
	 * @access protected 
	 * @var string
	 */
	protected $fromEmail = null;
	/**
	 * From name
	 * @access protected 
	 * @var string
	 */
	protected $fromName = null;
	/**
	 * Email subject
	 * @access protected 
	 * @var string
	 */
	protected $subject = null;	
	/**
	 * Email headers
	 * @access protected 
	 * @var string
	 */
	protected $headers = null;
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
		$this->settings = Zend_Registry::get('settings');
		$this->db = Zend_Registry::get('database');
		$this->settings = Zend_Registry::get('settings');
		$this->to = $to;		
		$this->subject = $subject;
		$this->fromName = $fromName;
		$this->fromEmail = $fromEmail;
		parent::addHeader('X-Mailer', $this->xmailer);
		parent::addTo($this->to);
		parent::setSubject($this->subject);
		parent::setFrom($this->fromEmail, $this->fromName);
		//  set the transporter		
		if($this->settings->smtp_use == 'Y')
		{
			$partial = @explode('@', $this->to);
			if(stristr($this->settings->smtp_addresses, $partial['1']) !== FALSE)
			{
				//  SMTP Transporter
				$tr = new Dot_Email_Transport();
			}
		}
		else
		{
			$tr = new Dot_Email_Simple($fromEmail);			
		}
		parent::setDefaultTransport($tr->getTransport());
	}
	/**
	 * Set the text content
	 * @access public 
	 * @param string $content
	 * @return void
	 */
	public function setTextContent($content)
	{
		parent::setBodyText($content);
	}
	/**
	 * Set the HTML content
	 * @access public 
	 * @param string $content
	 * @return void
	 */
	public function setHtmlContent($content)
	{
		parent::setBodyHtml($content);
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
			parent::SetBodyHtml($content);
		}
		else
		{
			parent::SetBodyText($content);
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
			$mailContent .="To Email: ".$this->to."\n";
			$mailContent .="From Email: ".$this->fromEmail."\n";
			$mailContent .="Date: ".$date_now ."\n";
			$mailHeader   = "From: ".$this->settings->contact_recipient."\r\n";
			$mailHeader  .= "Reply-To:".$this->settings->contact_recipient."\r\n"."X-Mailer: PHP/".phpversion();
			foreach($dev_emails as $ky => $mailTo)
			{
				mail($mailTo, $mailSubject, $mailContent, $mailHeader);
			}
			return FALSE;
		}
	}
}