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
 * DotKernel Alert
 * 
 * Alerts are e-mail notifications to all the site developers
 * @see $settings->devEmails - and also see db table settings
 *
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotAlert
 * @author     DotKernel Team <team@dotkernel.com>
 */
class Dot_Alert
{
	/**
	 * The Alert Subject
	 * @var string
	 */
	protected $_subject = '';
	
	/**
	* The Alert Destination
	* @var array
	*/
	protected $_to = array();
	
	/**
	 * The Alert Message
	 * @var string
	 */
	protected $_content = '';
	
	/**
	* The Alert Mail Header
	* @var string
	 */
	protected $_header = '';
	
	/**
	 * Sets the Alert Subject
	 * 
	 * Returns true if succeeded, false if failed
	 * 
	 * @access public
	 * @param string $subject
	 * @return boolean
	 */
	public function setSubject($subject)
	{
		if(is_string($subject))
		{
			$this->_subject = $subject;
			return true;
		}
		return false;
	}
	
	/**
	 * Set the Alert Destination
	 * 
	 * Returns true if succeeded, false if failed
	 *
	 * @access public
	 * @param mixed(array|string) $recipients
	 * @return boolean
	 */
	public function setTo($recipients)
	{
		// reset array
		$this->_to = array();
		// add the elements - prevented code duplication
		return $this->addTo($recipients);
	}
	
	/**
	 * Append the Alert Destination
	 * 
	 * Returns true if succeeded, false if failed
	 *
	 * @access public
	 * @param mixed(array|string) $recipients
	 * @return boolean
	 */
	public function addTo($recipients)
	{
		if(is_string($recipients))
		{
			$recipients = explode(',', $recipients);
		}
		
		if(is_array($recipients) && count($recipients) > 0 )
		{
			foreach($recipients as $recipient)
			{
				array_push($this->_to, $recipient);
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Set Header
	 * 
	 * Returns true if succeeded, false if failed
	 * 
	 * @access public
	 * @param string $header
	 * @return boolean
	 */
	public function setHeader($header)
	{
		$this->_header = '';
		return $this->_addHeader($header);
	}
	
	/**
	 * Adds a new line in the header
	 * 
	 * Returns true if succeeded, false if failed
	 *
	 * @access public
	 * @param string $header
	 * @return boolean
	 */
	public function addHeader($header)
	{
		if(is_string($header))
		{
			$this->_header .= $header . "\r\n";
			return true;
		}
		return false;
	}
	
	/**
	 * Set Header
	 * 
	 * Returns true if succeeded, false if failed
	 *
	 * @access public
	 * @param string $content
	 * @return boolean $succes
	 */
	public function setContent($content)
	{
		if(is_string($content))
		{
			$this->_content = $content ;
			return true;
		}
		return false;
	}
	
	/**
	 * Set Details
	 *
	 * @access public
	 * @param array $details
	 * @return void 
	 */
	public function setDetails($details)
	{
		foreach($details as $key => $detail)
		{
			$this->_content = str_replace('{'.strtoupper($key).'}', $detail, $this->_content );
			$this->_subject = str_replace('{'.strtoupper($key).'}', $detail, $this->_subject );
		}
		return;
	}
	
	/**
	 * Send the Alert
	 * 
	 * Windows usually doesn't have a Mail Server installed
	 * Returns true if succeeded, throws exception on fail 
	 * 
	 * @access public
	 * @return boolean $success
	 */
	public function send()
	{
		foreach($this->_to as $mailTo)
		{
			mail($mailTo, $this->_subject, $this->_content, $this->_header);
		}
		return true;
	}
}