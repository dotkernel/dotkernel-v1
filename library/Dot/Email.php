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
		$this->option = Zend_Registry::get('option');
		
		// plugin call here
		
		// transport 
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
	 * @param $plugin
	 * @return Zend_Mail_Transport_Abstract|bool
	 */
	protected function _getTransportFromPlugin(Plugin_Interface $plugin)
	{
		return $plugin->getTransporter();
	}
	
	/**
	 * Get the fallback Transport, used if the plugin is disabled
	 * @return Zend_Mail_Transport_Sendmail
	 */
	protected function _getFallBackTransport($parameters = null)
	{
		Zend_Mail::setDefaultFrom($this->settings->siteEmail);
		Zend_Mail::setDefaultReplyTo($this->settings->siteEmail, $this->seoOption->siteName);
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
			$message = $this->option->alertMessages->email;
			$devEmails = @explode(',', $this->settings->devEmails);
			
			// preparing the message details
			$details = array(
				'e_class' => get_class($e),
				'site_name' => $this->seoOption->siteName,
				'e_message' => $e->getMessage(),
				'to_email' => implode(',', $this->_to),
				'from_email' => $this->getFrom(),
				'date_now' => date('F dS, Y h:i:s A'),
			);
			
			// creating the alert
			$alert = new Dot_Alert();
			// send it to devs
			$alert->setTo($devEmails);
			$alert->setSubject("SMTP Error on " . $this->seoOption->siteName);
			// add the headers
			$alert->addHeader( "From: " . $this->settings->siteEmail);
			$alert->addHeader( "Reply-To:" . $this->settings->siteEmail );
			$alert->addHeader( "X-Mailer: PHP/" . phpversion() ) ;
			// prepare the message
			$alert->setContent($message);
			$alert->setDetails($details);
			$alert->send();
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