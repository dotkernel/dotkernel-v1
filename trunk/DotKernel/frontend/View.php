<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Frontend
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* View Model
* abstract over the Dot_Template class
* @category   DotKernel
* @package    Frontend
* @author     DotKernel Team <team@dotkernel.com>
*/

class View extends Dot_Template
{
	/**
	 * Singleton instance
	 * @access protected
	 * @static
	 * @var Dot_Template
	 */
	protected static $_instance = null;
	/**
	 * Returns an instance of Dot_View
	 * Singleton pattern implementation
	 * @access public
	 * @param string $root     Template root directory
	 * @param string $unknowns How to handle unknown variables
	 * @param array  $fallback Fallback paths
	 * @return Dot_Template
	 */
	public static function getInstance($root = '.', $unknowns = 'remove', $fallback='')
	{
		if (null === self::$_instance) 
		{
			self::$_instance = new self($root, $unknowns, $fallback);
		}
		return self::$_instance;
	}	
	/**
	 * Initalize some parameter
	 * @access public
	 * @return void
	 */	
	public function init()
	{
		$this->requestModule = Zend_Registry::get('requestModule');
		$this->requestController = Zend_Registry::get('requestController');
		$this->requestAction = Zend_Registry::get('requestAction');
		$this->config = Zend_Registry::get('configuration');
		$this->seo = Zend_Registry::get('seo');
	}
	/**
	 * Set the template file
	 * @access public 
	 * @return void
	 */
	public function setViewFile()
	{
		$this->setFile('tpl_index', 'index.tpl');
	}
	/**
	 * Set different paths url(site, templates, images)
	 * @access public
	 * @return void
	 */
	public function setViewPaths()
	{
		$this->setVar('TEMPLATES_URL', $this->config->website->params->url . TEMPLATES_DIR);
		$this->setVar('IMAGES_URL', $this->config->website->params->url . IMAGES_DIR . '/' .$this->requestModule);
		$this->setVar('SITE_URL', $this->config->website->params->url);
	}
	/**
	 * Set SEO values
	 * @access public
	 * @param string $pageTitle [optional]
	 * @return void
	 */
	public function setSeoValues($pageTitle = '')
	{
		$this->setVar('PAGE_KEYWORDS', $this->seo->defaultMetaKeywords);
		$this->setVar('PAGE_DESCRIPTION', $this->seo->defaultMetaDescription);
		$this->setVar('PAGE_TITLE', $this->seo->defaultPageTitle .  ' | ' . $pageTitle);
		$this->setVar('PAGE_CONTENT_TITLE', $pageTitle);
		$this->setVar('SITE_NAME', $this->seo->siteName);
		$this->setVar('CANONICAL_URL', $this->seo->canonicalUrl);
	}
	/**
	 * Display the menus
	 * @access public 
	 * @return void
	 */
	public function setMenu()
	{
		$dotAuth = Dot_Auth::getInstance();
		$registry = Zend_Registry::getInstance();
		
		// this template variable will be replaced with "selected"
		$selectedItem = "SEL_" . strtoupper($registry->requestController . "_" . $registry->requestAction);
		
		// top menu
		$this->setFile('tpl_menu_top', 'blocks/menu_top.tpl');
		$this->setBlock('tpl_menu_top', 'top_menu_not_logged', 'top_menu_not_logged_block');
		$this->setBlock('tpl_menu_top', 'top_menu_logged', 'top_menu_logged_block');

		// add selected to the correct menu item
		$this->setVar($selectedItem, 'selected');
		
		if ($dotAuth->hasIdentity('user'))
		{
			$this->parse('top_menu_logged_block', 'top_menu_logged', true);
			$this->parse('top_menu_not_logged_block', '');		
		}
		else
		{
			$this->parse('top_menu_not_logged_block', 'top_menu_not_logged', true);
			$this->parse('top_menu_logged_block', '');		
		}
		$this->parse('MENU_TOP', 'tpl_menu_top');
		
		// sidebar menu
		$this->setFile('tpl_menu_sidebar', 'blocks/menu_sidebar.tpl');
		$this->setBlock('tpl_menu_sidebar', 'sidebar_menu_logged', 'sidebar_menu_logged_block');
		$this->setBlock('tpl_menu_sidebar', 'sidebar_menu_not_logged', 'sidebar_menu_not_logged_block');

		// add selected to the correct menu item
		$this->setVar($selectedItem, 'selected');
		
		if ($dotAuth->hasIdentity('user'))
		{
			$this->parse('sidebar_menu_logged_block', 'sidebar_menu_logged', true);
			$this->parse('sidebar_menu_not_logged_block', '');		
		}
		else
		{
			$this->parse('sidebar_menu_not_logged_block', 'sidebar_menu_not_logged', true);
			$this->parse('sidebar_menu_logged_block', '');		
		}

		$this->parse('MENU_SIDEBAR', 'tpl_menu_sidebar');

		// footer menu
		$this->setFile('tpl_menu_footer', 'blocks/menu_footer.tpl');

		// add selected to the correct menu item
		$this->setVar($selectedItem, 'selected');
		
		$this->parse('MENU_FOOTER', 'tpl_menu_footer');
	}
	/**
	 * Display message - error, warning, info
	 * @access public
	 * @return void
	 */
	public function displayMessage()
	{
		$session = Zend_Registry::get('session');
		if(isset($session->message))
		{
			$this->setFile('tpl_msg', 'blocks/message.tpl');
			$this->setBlock('tpl_msg', 'msg_array', 'msg_array_row');
			$this->setVar('MESSAGE_TYPE', $session->message['type']);
			if(is_array($session->message['txt']))
			{
				foreach ($session->message['txt'] as $k => $msg)
				{
					$this->setVar('MESSAGE_ARRAY', is_string($k) ? $msg = ucfirst($k) . ' - ' . $msg : $msg);
					$this->parse('msg_array_row', 'msg_array', true);
				}
			}
			else
			{
				$this->parse('msg_array_row', '');
				$this->setVar('MESSAGE_STRING', $session->message['txt']);
			}
			$this->parse('MESSAGE_BLOCK', 'tpl_msg');
			unset($session->message);
		}		
	}
	/**
	 * Add the user's token to the template
	 * @access public
	 * @return array
	 */
	public function addUserToken()
	{
		$dotAuth = Dot_Auth::getInstance();
		$user = $dotAuth->getIdentity('user');
		$this->setVar('USERTOKEN', Dot_Auth::generateUserToken($user->password));
	}	
	/**
	 * Get captcha display box using Zend_Service_ReCaptcha api
	 * @access public
	 * @return Zend_Service_ReCaptcha
	 */
	public function getRecaptcha()
	{
		$option = Zend_Registry::get('option');
		// add secure image using ReCaptcha
		$recaptcha = new Zend_Service_ReCaptcha($option->captchaOptions->recaptchaPublicKey, $option->captchaOptions->recaptchaPrivateKey);
		$recaptcha->setOptions($option->captchaOptions->toArray());
		return $recaptcha;
	}
}