<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
* @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* User Model
* Here are all the actions related to the user
* @category   DotKernel
* @package    Admin 
* @author     DotKernel Team <team@dotkernel.com>
*/
class User extends Dot_Model_User
{
	/**
	 * Constructor
	 * @access public
	 */
	public function __construct()
	{
		parent::__construct();
		$this->settings = Zend_Registry::get('settings');			
		$seo = new Dot_Seo();
		$this->seoOption = $seo->getOption();	
	}	
	/**
	 * Get user list
	 * @access public 
	 * @param int $page [optional]
	 * @return array	 
	 */
	public function getUserList($page = 1)
	{
		$select = $this->db->select()
						   ->from('user');				
 		$dotPaginator = new Dot_Paginator($select, $page, $this->settings->resultsPerPage);
		return $dotPaginator->getData();
	}	
	/**
	 * Delete user
	 * @param int $id
	 * @return void
	 */
	public function deleteUser($id)
	{
		$this->db->delete('user', 'id = ' . $id);
	}	
	/**
	 * Send forgot password to user
	 * @access public
	 * @param int id
	 * @return void
	 */
	public function sendPassword($id)
	{
		$session = Zend_Registry::get('session');
		$value = $this->getUserBy('id', $id);
		if(!empty($value))
		{
			$dotEmail = new Dot_Email();
			$dotEmail->addTo($value['email']);
			$dotEmail->setSubject($this->seoOption->siteName . ' - ' . $this->option->forgotPassword->subject);
			$msg = str_replace(array('%FIRSTNAME%', '%PASSWORD%'), 
							   array($value['firstName'], $value['password']), 
				              $this->option->forgotPassword->message);
			$dotEmail->setBodyText($msg);		
			$succeed = $dotEmail->send();
			if($succeed)
			{
				$session->message['txt'] = $this->option->infoMessage->emailSent.$value['email'];
				$session->message['type'] = 'info';
			}
			else
			{
				$session->message['txt'] = $this->option->errorMessage->emailNotSent.$value['email'];
				$session->message['type'] = 'error';
			}		
		}
		else
		{
			$session->message['txt'] = $value['email'].$this->option->infoMessage->emailNotFound;
			$session->message['type'] = 'info';
		}		
	}
	/**
	 * Activate/Inactivate user account
	 * @param int $id - user ID
	 * @param int $isActive
	 * @return void
	 */
	public function activateUser($id, $isActive)
	{		
        $this->db->update('user', array('isActive' => $isActive), 'id = '.$id);
	}
	/**
	 * Get admin users logins archive list
	 * @access public
	 * @param int $id 
	 * @param int $page [optional]
	 * @param string $browser [optional]
	 * @return array
	 */
	public function getLogins($id, $page = 1, $browser = '', $loginDate = '', $sortField = '', $orderBy = '')
	{
		$select = $this->db->select()
						->from('userLogin')
						->joinLeft(
							'user',
							'userLogin.userId = user.id',
							'username'
						);
		if ($id > 0) 
		{
			$select->where('userId = ?', $id);
		}
		if ($browser != '')
		{
			$select->where($this->db->quoteInto("userAgent LIKE ? ", '%'.$browser.'%'));
		}
		if ($loginDate != '')
		{
			$select->where('dateLogin LIKE ?', '%'.$loginDate.'%');
		}
		if ($sortField!=""){
			$select->order($sortField. ' '.$orderBy);
		}		
		$dotPaginator = new Dot_Paginator($select, $page, $this->settings->resultsPerPage);
		return $dotPaginator->getData();
		
	}	
	/**
	 * Get top country user logins as declared in 
	 * $option->countCountryUserLogin
	 * @access public
	 * @return array
	 */
	public function getTopCountryLogins($topCount)
	{
		$select = $this->db->select()
					  	   ->from('userLogin');
		$logins = $this->db->fetchAll($select);
		$countryName = array();
		$countryCount = array();
		foreach ($logins as $v)
		{
			if(array_key_exists($v['country'], $countryCount))
			{
				$countryCount[$v['country']]++;
			}
			else
			{
				 $countryCount[$v['country']] = 1;
			}
		}
		arsort($countryCount);
		$countSum = array_sum($countryCount);
		$i = 1;
		$data['Other'] = array('count' => 0, 'countPercent' => 0,'name' => 'Others');
		foreach ($countryCount as $country => $count)
		{
			$countPercent = round($count * 100 / $countSum, 2);
			if($i >= $topCount)
			{
				$data['Other']['countPercent'] += $countPercent; 
				$data['Other']['count'] += $count; 
			}
			else
			{
				$data[$country]['countPercent'] = $countPercent; 
				$data[$country]['count'] = $count; 
				
			}
			$i++;
		}
		if(!$data['Other']['count'])
		{
			unset($data['other']);
		}
		return $data;
	}
}
