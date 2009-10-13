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
	private $settings = null;
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
	 * @param string $from [optional]
	 * @param string $subject [optional]
	 * @param string $headers [optional]
	 * @return void
	 */
	public function __construct($config, $to = null, $from = null, $subject = null, $headers = null)
	{
		$this->settings = $config;
		$this->to = $to;
		$this->to = $to;
		$this->subject = $subject;
		$this->fromEmail = $from;
		//  this one is not used anymore
		$this->headers = $headers;
		//  get FromName and FromEmail, crap code for backward compatibility
		$from = @explode('<', $from);
		if( is_array($from) )
		{
			$this->fromName = $from['0'];
			$this->fromEmail =  rtrim($from['1'],'>');
		}
		parent::addHeader('X-Mailer', $this->xmailer);
		parent::addTo($this->to);
		parent::setSubject($this->subject);
		//  set the transporter
		//  check if we can use regular server sendmail
		$partial = @explode('@', $this->to);
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
		if(strlen(trim(strval($this->bcc))) > 0)
		{
			parent::addBcc($this->bcc);
		}
		/**
		 * @TODO is this the proper error trapping system 		 
		 */
		try
		{
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
}