<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotLibrary
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
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
	 * Transport used for Mail
	 * @access protected
	 * @var Zend_Mail_Transport_Abstract
	 */
	protected $_transport = null;
	
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
		$this->_transport = $this->_getTransport();
		if(!$this->_transport)
		{
			$this->_transport = $this->_getFallBackTransport();
		}
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
	 * Get Transport From Plugin
	 * If no Transport was found, false returned 
	 * @access protected
	 * @return Zend_Mail_Transport_Abstract|bool
	 */
	protected function _getTransport()
	{
		$pluginLoader = Plugin_Loader::getInstance();
		if($pluginLoader->isPluginEnabled('DotBoost', 'MailTransporter'))
		{
			$plugin = $pluginLoader->loadPlugin('DotBoost', 'MailTransporter');
			$transport = $plugin->getTransporter();
			return $transport;
		}
		return false;
	}
	
	/**
	 * Get the fallback Transport, used if the plugin is disabled
	 * @return Zend_Mail_Transport_Sendmail
	 */
	protected function _getFallBackTransport($parameters = null)
	{
		return new Zend_Mail_Transport_Sendmail($parameters);
	}
	
	/**
	 * Send email. Parameter is included only to be compatible with Zend_Mail
	 * @access public 
	 * @param  Zend_Mail_Transport_Abstract $transport [optional]
	 * @return bool
	 */
	public function send($transport = null)
	{
		parent::setDefaultTransport($this->_transport);
		try
		{
			parent::send();
			return true;
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
			foreach($devEmails as $mailTo)
			{
				mail($mailTo, $mailSubject, $mailContent, $mailHeader);
			}
			return false;
		}
	}
	
	/**
	 * Sets the "to" parameter
	 * 
	 * CAUTION!!! Do not call this function twice
	 * This function deletes the previous TO recipients
	 * 
	 * @access public
	 * @param string|array $to
	 * @return Dot_Email
	 */
	public function setTo($email, $name = '')
	{
		$this->_to = array();
		$this->addTo($email, $name);
		return $this;
	}
}