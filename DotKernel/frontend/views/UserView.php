<?php
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
*
* @category   DotKernel
* @package    Frontend 
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* User View Class
* class that prepare output related to User controller 
* @category   DotKernel
* @package    Frontend
* @author     DotKernel Team <team@dotkernel.com>
*/

class User_View extends View
{
	/**
	 * Constructor
	 * @access public
	 * @param Dot_Template $tpl
	 */
	public function __construct($tpl, $settings)
	{
		$this->tpl = $tpl;
		$this->settings = $settings;
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
	 * Display user's signup form
	 * @access public
	 * @param string $templateFile
	 * @return void
	 */
	public function details($templateFile, $data=array(), $error=array())
	{
		$this->tpl->setFile('tpl_main', 'user/' . $templateFile . '.tpl');				
		foreach ($data as $k=>$v)
		{
		    $this->tpl->setVar(strtoupper($k), $v);
		}
		if('add' == $templateFile)
		{
			$this->tpl->setVar('SECUREIMAGE',$this->getRecaptcha());			
		}
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
	public function getRecaptcha()
	{
		// add secure image using ReCaptcha
		$recaptcha = new Zend_Service_ReCaptcha($this->settings->recaptcha_public_key, $this->settings->recaptcha_private_key);
		$recaptcha->setOptions(array('linkTitle' => 'Click me'));
		return $recaptcha;
	}
}
