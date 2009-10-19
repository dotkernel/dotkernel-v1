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
* Default server mail() class 
* @category   DotKernel
* @package    DotLibrary
* @subpackage DotEmail
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Email_Simple extends Dot_Email
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
		$this->settings = Zend_Registry::get('settings');
		$this->to = $to;		
		$this->subject = $subject;
		$this->fromName = $fromName;
		$this->fromEmail = $fromEmail;
		parent::addHeader('X-Mailer', $this->xmailer);
		parent::addTo($this->to);
		parent::setSubject($this->subject);
		//  Sendmail transporter
		$transport = new Zend_Mail_Transport_Sendmail('-f'.$this->fromEmail);
		parent::setDefaultTransport($transport);
		parent::setFrom($this->fromEmail, $this->fromName);
		
	}
	/**
	 * Send email
	 * @access public 
	 * @return void
	 */
	public function send()
	{			
		parent::send();
	}
}