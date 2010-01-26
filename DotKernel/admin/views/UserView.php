<?php
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
*
* @category   DotKernel
* @package    Admin 
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* User View Class
* class that prepare output related to User controller 
* @category   DotKernel
* @package    Admin 
* @author     DotKernel Team <team@dotkernel.com>
*/

class User_View extends View
{
	/**
	 * Constructor
	 * @access public
	 * @param Dot_Template $tpl
	 */
	public function __construct($tpl)
	{
		$this->tpl = $tpl;
	}
	/**
	 * Display the login form
	 * @access public
	 * @param string $templateFile
	 * @return void
	 */
	public function loginForm($templateFile)
	{
		$this->tpl->setFile('tpl_main', 'user/' . $templateFile . '.tpl');
		if(array_key_exists('login_user', $_SESSION['kernel']))
		{
			$this->tpl->setVar('ERROR',$_SESSION['kernel']['login_user']);
			unset($_SESSION['kernel']['login_user']);
		}
	}
	/**
	 * List the admin users
	 * @access public
	 * @param string $templateFile
	 * @param array $users
	 * @return void
	 */
	public function listUser($templateFile, $users)
	{
		$this->tpl->setFile('tpl_main', 'user/' . $templateFile . '.tpl');
		$this->tpl->setBlock('tpl_main', 'list', 'list_block');
		$this->tpl->paginator($users,'page',1);
		foreach ($users as $k => $v)
		{
		    $this->tpl->setVar('BG', $k%2+1);
			$this->tpl->setVar('ID', $v['id']);
			$this->tpl->setVar('USERNAME', $v['username']);
			$this->tpl->setVar('EMAIL', $v['email']);
			$this->tpl->setVar('FIRSTNAME', $v['firstname']);
			$this->tpl->setVar('LASTNAME', $v['lastname']);
			$this->tpl->setVar('DATE_CREATED', $v['date_created']);
			$this->tpl->setVar('ACTIVE_IMG', $v['active'] == 1 ? 'active' : 'inactive');
			$this->tpl->parse('list_block', 'list', true);
		}
	}
	/**
	 * Display users details. It is using for add and update actions
	 * @access public
	 * @param string $templateFile
	 * @param array $data [optional]
	 * @param array $error [optional]
	 * @return void
	 */
	public function details($templateFile, $data=array(), $error=array())
	{
		$this->tpl->setFile('tpl_main', 'user/' . $templateFile . '.tpl');				
		foreach ($data as $k=>$v)
		{
		    $this->tpl->setVar(strtoupper($k), $v);
		}
		//empty because password is encrypted with md5
		$this->tpl->setVar('PASSWORD', '');
		$errorMessage = '';
		if(!empty($error))
		{
			foreach ($error as $k=>$v)
			{
			    $errorMessage .= '<b>'.ucfirst($k).':</b> '.$v.'<br />';
			}
		}
		$this->tpl->setVar('ERROR',$errorMessage);
	}
}
