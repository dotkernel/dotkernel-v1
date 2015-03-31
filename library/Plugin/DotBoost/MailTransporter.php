<?php
/**
 * DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id: Email.php 872 2015-01-05 16:34:50Z gabi $
*/

/**
 * Alternate SMTP and default server mail() class
* @category   DotKernel
* @package    DotPlugin
* @subpackage Smtp_Mailer 
* @author     DotKernel Team <team@dotkernel.com>
*/

class Plugin_DotBoost_MailTransporter extends Plugin_Abstract
{
	
	const PLUGIN_VENDOR = 'DotBoost';
	const PLUGIN_NAME = 'Mailer';
	const PLUGIN_VERSION = '1.0.0';
	
	/**
	 * Get plugin info
	 * @access public
	 * @return array $info
	 */
	public function getPluginInfo()
	{
		$info = array(
			'vendor'=>self::PLUGIN_VENDOR ,
			'name'=>self::PLUGIN_NAME,
			'version'=>self::PLUGIN_VERSION
		);
		return $info;
	}
	
	/**
	 * Gets a transporter from the factory
	 * 
	 * This function also sets the from & reply-to values
	 * Theese values can be changed using Dot_Email -> setFrom setReplyTo
	 * 
	 * @return Zend_Mail_Transport_Abstract
	 */
	public function getTransporter()
	{
		// Here is a sample of how we link with the actual plugin
		// This is only a Plugin Handler Class
		$transporter = Plugin_DotBoost_MailTransporter_Factory::createTransporter($this->_options['smtpActive'], $this->_options['siteEmail']);
		Zend_Mail::setDefaultFrom($this->_options['siteEmail'], $this->_options['fromName']);
		Zend_Mail::setDefaultReplyTo($this->_options['siteEmail'], $this->_options['fromName']);
		return $transporter;
	}
}