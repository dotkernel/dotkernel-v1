<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
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
		$seoOption = Zend_Registry::get('seo');
		$value = $this->getUserBy('id', $id);
		if(!empty($value))
		{
			$dotEmail = new Dot_Email();
			$dotEmail->addTo($value['email']);
			$dotEmail->setSubject($seoOption->siteName . ' - ' . $this->option->forgotPassword->subject);
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
	 * @param string $loginDate [optional]
	 * @param string $sortField [optional]
	 * @param string $orderBy [optional]
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
		if ($sortField!="")
		{
			$select->order($sortField. ' '.$orderBy);
		}
		$dotPaginator = new Dot_Paginator($select, $page, $this->settings->resultsPerPage);
		return $dotPaginator->getData();

	}
	/**
	 * Get top <topCount> logins by country
	 * If there are more countries returned than <topCountry>, the sum of the remainder
	 * will be added to $result['Other']
	 * @param topCount - the number of countries to return
	 * @access public
	 * @return array
	 */
	public function getTopCountryLogins($topCount)
	{
		$select = $this->db->select()
					  	   ->from(
					  	   		'userLogin',
					  	   		array('country', 'cnt'=>'COUNT(country)')
					  	   )
					  	   ->group('country')
					  	   ->order('cnt DESC');
		$logins = $this->db->fetchAll($select);
		$data = array();
		foreach($logins as $login)
		{
			$data[] = array(
				'label' => $login['country'],
				'data' =>  intval($login['cnt'])
			);
		}

		if (count($data) > $topCount)
		{
			$others = array_splice($data, $topCount);
			$otherCount = 0;
			foreach ($others as $other)
			{
				$otherCount += $other['data'];
			}
			$data[] = array(
				'label' => 'Other',
				'data' => $otherCount
			);
		}

		return $data;
	}
	
	/**
	 * Get top <topCount> users by logins
	 * @param topCount - the number of users to return
	 * @access public
	 * @return array
	 */
	public function getTopUsersByLogins($topCount)
	{
		$select = $this->db->select()
							->from(array('a' => 'userLogin'),
									array('b.username', 'cnt'=>'COUNT(a.userId)'))
							->joinLeft(array('b' => 'user'), 'a.userId = b.id', array())
							->group('userId')
							->order('cnt DESC')
							->limit($topCount);
		$logins = $this->db->fetchAll($select);
		$data = array();
		foreach($logins as $login)
		{
			$data[] = array(
					'label' => $login['username'],
					'data' =>  intval($login['cnt'])
			);
		}
		return $data;
	}
}
