<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
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

class Dot_Email_Sendmail extends Dot_Email
{
	/**
	 * Email constructor
	 * @access public 
	 * @param string $fromEmail [optional]
	 * @return Dot_Email_Sendmail
	 */
	public function __construct($fromEmail = null)
	{
		$this->transport = new Zend_Mail_Transport_Sendmail($fromEmail);
	}
	/**
	 * Return the transporter
	 * @access public
	 * @return Zend_Mail_Transport_Sendmail
	 */
	public function getTransport()
	{
		return $this->transport;
	}
}