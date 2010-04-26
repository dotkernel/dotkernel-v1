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
* Admin View Class
* class that prepare output related to User controller 
* @category   DotKernel
* @package    Admin 
* @author     DotKernel Team <team@dotkernel.com>
*/

class Admin_View extends View
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
		$session = Zend_Registry::get('session');
		$this->tpl->setFile('tpl_main', 'admin/' . $templateFile . '.tpl');		
		if(isset($session->loginUserError))
		{
			$this->tpl->setVar('MESSAGE_TEXT',$session->loginUserError);
			$this->tpl->setVar('MESSAGE_TYPE', 'error');
			unset($session->loginUserError);
		}
	}
	/**
	 * List the admin users
	 * @access public
	 * @param string $templateFile
	 * @param array $list
	 * @param int $page
	 * @return void
	 */
	public function listUser($templateFile, $list, $page)
	{
		$this->tpl->setFile('tpl_main', 'admin/' . $templateFile . '.tpl');
		$this->tpl->setBlock('tpl_main', 'list', 'list_block');
		$this->tpl->paginator($list['paginatorAdapter'],$page);
		foreach ($list['data'] as $k => $v)
		{
		    $this->tpl->setVar('BG', $k%2+1);
			$this->tpl->setVar('ID', $v['id']);
			$this->tpl->setVar('USERNAME', $v['username']);
			$this->tpl->setVar('EMAIL', $v['email']);
			$this->tpl->setVar('FIRSTNAME', $v['firstName']);
			$this->tpl->setVar('LASTNAME', $v['lastName']);
			$this->tpl->setVar('DATE_CREATED', $v['dateCreated']);
			$this->tpl->setVar('ACTIVE_IMG', $v['isActive'] == 1 ? 'active' : 'inactive');
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
		$this->tpl->setFile('tpl_main', 'admin/' . $templateFile . '.tpl');				
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
			$this->tpl->setVar('MESSAGE_TYPE', 'error');
		}
		$this->tpl->setVar('MESSAGE_TEXT', $errorMessage);
	}
}
