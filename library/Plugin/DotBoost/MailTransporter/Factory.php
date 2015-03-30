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
class Plugin_DotBoost_MailTransporter_Factory
{
	/**
	 * Transporter creator
	 * 
	 * If $smtpActive is true, a SMTP server data from db will be returned 
	 * If the $smtpActive is false or the SMTP wasn't found, a SendMail Transporter will be returned
	 * 
	 * @param string $fromEmail
	 * @throws Zend_Mail_Transport_Exception
	 * @return Zend_Mail_Transport_Abstract|bool
	 */
	public static function createTransporter($smtpActive, $fromEmail)
	{
		if($smtpActive)
		{
			$smtpData = self::_getSmtp();
			if(!empty($smtpData))
			{
				$mailConfig = array(
					'auth' => 'login',
					'username' => $smtpData['smtpUsername'],
					'password' => $smtpData['smtpPassword'],
					'port' => $smtpData['smtpPort'],
					'ssl' => $smtpData['smtpSsl']
				);
				self::_updateSmtpCounter($smtpData['id']);
				return new Zend_Mail_Transport_Smtp($smtpData['smtpServer'], $mailConfig);
			}
		}
		return new Zend_Mail_Transport_Sendmail($fromEmail);
	}

	/**
	 * Get the current SMTP info for sending the email
	 * @access private
	 * @static
	 * @return array
	 */
	private static function _getSmtp ()
	{
		$db = Zend_Registry::get('database');
		
		$smtp = array();
		$mapArray = array('id',
					'smtpUsername' => 'user',
					'smtpPassword' => 'pass',
					'smtpServer' => 'server',
					'smtpPort' => 'port',
					'smtpSsl' => 'ssl');
		
		$select = $db->select()
			->from('emailTransporter',$mapArray)
			->where('counter < capacity')
			->where('isActive = ?','1')
			->order('id')
			->limit('1');
		
		$result = $db->fetchAll($select);
		if (count($result) > 0)
		{
			$smtp = $result[0];
		}
		else
		{
			$where = array(" `date` < DATE_FORMAT( NOW( ) , '%Y-%m-%d' )","isActive = '1'");
			$db->update('emailTransporter', array('counter'=>0), $where);
			$db->update('emailTransporter', array('date'=>new Zend_Db_Expr('NOW()')), $where);
			$select->where("`date` = DATE_FORMAT( NOW( ) , '%Y-%m-%d' )");
			$result = $db->fetchAll($select);
			if (count($result) > 0)
			{
				$smtp = $result[0];
			}
		}
		return $smtp;
	}
	/**
	 * Update the counter of the current transporter.
	 * @access private
	 * @static
	 * @param int $id
	 * @return void
	 */
	private static function _updateSmtpCounter ($id)
	{
		$db = Zend_Registry::get('database');
		$db->update('emailTransporter', array('counter' => new Zend_Db_Expr('counter+1')), 'id = '.$id);
	}
}
