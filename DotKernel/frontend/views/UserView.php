<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
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
	public function __construct($tpl)
	{
		$this->tpl = $tpl;
		$this->settings = Zend_Registry::get('settings');
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
	}
	/**
	 * Display user's signup form
	 * @access public
	 * @param string $templateFile
	 * @param array $data [optional]
	 * @return void
	 */
	public function details($templateFile, $data=array())
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
	}
	public function getRecaptcha()
	{
		$option = Zend_Registry::get('option');
		// add secure image using ReCaptcha
		$recaptcha = new Zend_Service_ReCaptcha($option->captchaOptions->recaptchaPublicKey, $option->captchaOptions->recaptchaPrivateKey);
		$recaptcha->setOptions($option->captchaOptions->toArray());
		return $recaptcha;
	}
}
