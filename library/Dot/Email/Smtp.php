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
 * Alternate SMTP server mail() class
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotEmail
 * @see		  Dot_Email
 * @author     DotKernel Team <team@dotkernel.com>
 */

class Dot_Email_Smtp extends Dot_Email
{
	/**
	 * Email constructor
	 * @access public
	 * @param string $to [optional]
	 * @param string $fromName [optional]
	 * @param string $fromEmail [optional]
	 * @param string $subject [optional]
	 * @return Dot_Email_Smtp
	 */
	public function __construct($to = null, $fromName = null, $fromEmail = null, $subject = null)
	{
		$this->db = Zend_Registry::get('database');
		$this->smtpData = $this->_getSmtp();
		// check if we still have available SMTP connections for today
		if(!empty($this->smtpData))
		{
			$mailConfigs = array('auth' => 'login',
										   'username' => $this->smtpData['smtpUsername'],
										   'password' => $this->smtpData['smtpPassword'],
										   'port' => $this->smtpData['smtpPort'],
									       'ssl' => $this->smtpData['smtpSsl']);
			$this->transport = new Zend_Mail_Transport_Smtp($this->smtpData['smtpServer'], $mailConfigs);
		}
	}
	/**
	 * Return the transporter
	 * @access public
	 * @return Zend_Mail_Transport_Smtp
	 */
	public function getTransport()
	{
		$this->_updateSmtpCounter($this->smtpData['id']);
		return $this->transport;
	}
	/**
	 * Get the current SMTP info for sending the email
	 * @access private
	 * @return array
	 */
	private function _getSmtp ()
	{
		$smtp = array();
		$select = $this->db->select()
						   ->from('emailTransporter',
							 			  array('id',
												'smtpUsername' => 'user',
												'smtpPassword' => 'pass',
												'smtpServer' => 'server',
												'smtpPort' => 'port',
												'smtpSsl' => 'ssl'))
						   ->where('counter < capacity')
						   ->where('isActive = ?','1')
						   ->order('id')
						   ->limit('1');
		$result = $this->db->fetchAll($select);
		if (count($result) > 0)
		{
			$smtp = $result[0];
		}
		else
		{
			$where = array(" `date` < DATE_FORMAT( NOW( ) , '%Y-%m-%d' )","isActive = '1'");
			$this->db->update('emailTransporter', array('counter'=>0), $where);
			$this->db->update('emailTransporter', array('date'=>new Zend_Db_Expr('NOW()')), $where);
			$select->where("`date` = DATE_FORMAT( NOW( ) , '%Y-%m-%d' )");
			$result = $this->db->fetchAll($select);
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
	 * @param int $id
	 * @return void
	 */
	private function _updateSmtpCounter ($id)
	{
		$this->db->update('emailTransporter', array('counter' => new Zend_Db_Expr('counter+1')), 'id = '.$id);
	}
}