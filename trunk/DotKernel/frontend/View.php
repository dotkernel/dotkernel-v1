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
		if (null === self::$_instance) {
			self::$_instance = new self($root, $unknowns, $fallback);
		}
		return self::$_instance;
	}	
	/**
	 * Initalize some parameter
	 * @access public
	 * @param string $requestModule
	 * @param string $requestController
	 * @param string $requestAction
	 * @return void
	 */	
	public function init($requestModule, $requestController, $requestAction)
	{
		$this->requestModule = $requestModule;
		$this->requestController = $requestController;
		$this->requestAction = $requestAction;
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
	 * @param Zend_Config_Ini $config
	 * @return void
	 */
	public function setViewPaths($config)
	{
		$this->setVar('TEMPLATES_URL', $config->website->params->url . TEMPLATES_DIR);
		$this->setVar('IMAGES_URL', $config->website->params->url . IMAGES_DIR . '/' .$this->requestModule);
		$this->setVar('SITE_URL', $config->website->params->url);
	}
	/**
	 * Set SEO values
	 * @access public
	 * @param string $pageTitle [optional]
	 * @return void
	 */
	public function setSeoValues($pageTitle = '')
	{
		$seo = new Dot_Seo();
		$option = $seo->getOption();
		$this->setVar('PAGE_KEYWORDS', $option['defaultMetaKeywords']);
		$this->setVar('PAGE_DESCRIPTION', $option['defaultMetaDescription']);
		$this->setVar('PAGE_TITLE', $pageTitle .  ' | ' . $option['defaultPageTitle']);
		$this->setVar('PAGE_CONTENT_TITLE', $pageTitle);
		$this->setVar('SITE_NAME', $option['siteName']);
		$this->setVar('CANONICAL_URL', $option['canonicalUrl']);
	}
	/**
	 * Display the specific menu that were declared in configs/menu.xml file
	 * @access public 
	 * @param Zend_Config_Ini $config
	 * @param string $requestController
	 * @param string $requestAction
	 * @return void
	 */
	public function setViewMenu($config)
	{		
		$menu_xml = new Zend_Config_Xml(CONFIGURATION_PATH . '/' . $this->requestModule . '/' . 'menu.xml', 'config');
		$menu = $menu_xml->menu;
		// if we have only one menu, Zend_Config_Xml return a simple array, not an array with key 0(zero)
		if(is_null($menu->{0}))
		{
			$menu = new Zend_Config(array(0=>$menu_xml->menu));						
		}
		foreach ($menu as $child)
		{	
			//don't display the menu
			if(0 == $child->display) continue;		
			$this->setFile('tpl_menu_'.$child->id, 'blocks/menu_'.$child->type.'.tpl');
			//is not simple menu, so let's set the submenu blocks and variables
			if(strpos($child->type,'simple') === FALSE)
			{				
				$this->setBlock('tpl_menu_'.$child->id, 'top_sub_menu_item', 'top_sub_menu_item_block');
				$this->setBlock('tpl_menu_'.$child->id, 'top_sub_menu', 'top_sub_menu_block');
				$this->setBlock('tpl_menu_'.$child->id, 'top_normal_menu_item', 'top_normal_menu_item_block');
				$this->setBlock('tpl_menu_'.$child->id, 'top_parent_menu_item', 'top_parent_menu_item_block');
				$tplVariables1 = array('TOP_MENU_ID', 
									   'TOP_SUB_MENU_SEL', 
									   'TOP_SUB_MENU_ITEM_SEL', 
									   'TOP_SUB_MENU_LINK', 
									   'TOP_SUB_MENU_TARGET', 
									   'TOP_SUB_MENU_TITLE');				
				$tplBlocks1 = array('top_sub_menu_item_block', 
									'top_sub_menu_block', 
									'top_normal_menu_item_block', 
									'top_parent_menu_item_block');
			}			
			$this->setBlock('tpl_menu_'.$child->id, 'top_menu_item', 'top_menu_item_block');
			$this->setBlock('tpl_menu_'.$child->id, 'top_menu', 'top_menu_block');
			
			$tplVariables2 = array('TOP_MENU_SEL', 
								   'TOP_MENU_LINK', 
								   'TOP_MENU_TARGET', 
								   'TOP_MENU_TITLE');
			$tplBlocks2 = array('top_menu_item_block', 'top_menu_block');
			
			//Initialize all the tag variables and blocks
			$this->initVar(array_merge($tplVariables1,$tplVariables2),'');			
			$this->initBlock(array_merge($tplBlocks1,$tplBlocks2),'');
			
			$i = 0;					
			$items = $child->item;
			// if we have only one menu, Zend_Config_Xml return a simple array, not an array with key 0(zero)
			if(is_null($items->{0}))
			{
				$items = new Zend_Config(array(0=>$child->item));						
			}			
			foreach ($items as $key => $val)
			{						
				if ((Dot_Auth::hasIdentity('user') && 1 == $val->isLogged) || (!Dot_Auth::hasIdentity('user') && 1 == $val->notLogged))
				{	// display menus based on user is logged in or not
					$this->setVar('TOP_MENU_ID', $i);
					$tplVariables = array('TOP_MENU_SEL', 
					                      'TOP_SUB_MENU_SEL', 
										  'TOP_SUB_MENU_ITEM_SEL');
					$this->initVar($tplVariables,'');	
					if (false !== stripos($val->link, $this->requestController.'/'.$this->requestAction.'/'))
					{	//if current menu is the current viewed page
						$this->setVar('TOP_MENU_SEL', '_selected');
						$this->setVar('TOP_SUB_MENU_SEL', '_selected');
					}
					elseif('vertical' == $child->type && FALSE === strpos($child->type,'simple'))
					{
						$this->parse('top_sub_menu_block', '');
					}
					foreach ($val as $k => $v) 
					{	
						$this->setVar('TOP_MENU_'.strtoupper($k), is_string($v) ? $v : '');
					}	
					if (1 == $val->external) 
					{
						$this->setVar('TOP_MENU_LINK', $val->link);
					}
					else
					{
						$this->setVar('TOP_MENU_LINK', $config->website->params->url.'/'.$val->link);	
					} 
					if(FALSE === strpos($child->type,'simple'))
					{														
						if ((string)$val->link != '')
						{
							$this->parse('top_normal_menu_item_block', 'top_normal_menu_item', true);
						} 								
						else
						{
							$this->parse('top_parent_menu_item_block', 'top_parent_menu_item', true);
						} 
						if (isset($val->subItems->subItem) && count($val->subItems->subItem) > 0)
						{
												
							$subItems = $val->subItems->subItem;
							// if we have only one menu, Zend_Config_Xml return a simple array, not an array with key 0(zero)
							if(is_null($subItems->{0}))
							{
								$subItems = new Zend_Config(array(0=>$subItems));						
							}							
							foreach ($subItems as $k2 => $v2)
							{			
								if ((Dot_Auth::hasIdentity('user') && 1 == $v2->isLogged) || (!Dot_Auth::hasIdentity('user') && 1 == $v2->notLogged))
								{				
									// display menus based on user is logged in or not		
									$this->setVar('TOP_SUB_MENU_ITEM_SEL', '');												
									foreach ($v2 as $k => $v)
									{
										$this->setVar('TOP_SUB_MENU_'.strtoupper($k), is_string($v) ? $v : '');
									}
									if (1 == $v2->external) 
									{
										$this->setVar('TOP_SUB_MENU_LINK', $v2->link);
									}
									else 
									{
										$this->setVar('TOP_SUB_MENU_LINK', $config->website->params->url.'/'.$v2->link);
									}
									if (FALSE  !==stripos($v2->link, $this->requestController.'/'.$this->requestAction.'/'))
									{	//if curent menu is the curent viewed page then parent menu will be selected and sub menu shown
										$tplVariables = array('TOP_MENU_SEL', 
										                      'TOP_SUB_MENU_SEL', 
															  'TOP_SUB_MENU_ITEM_SEL');
										$this->initVar($tplVariables,'_selected');											
									}	
									$this->parse('top_sub_menu_item_block', 'top_sub_menu_item', true);												
								}
							}
						}						
						
					}
					if(strpos($child->type,'simple') === FALSE)
					{
						$this->parse('top_sub_menu_block', 'top_sub_menu', true);
						$this->parse('top_sub_menu_item_block', '');	
					}
					$this->parse('top_menu_item_block', 'top_menu_item', true);	
					if(strpos($child->type,'simple') === FALSE)
					{
						$this->parse('top_normal_menu_item_block', '');
						$this->parse('top_parent_menu_item_block', '');
					}												
					$i++;				
				}	
			}					
			$this->parse('top_menu_block', 'top_menu', true);
			$this->parse('top_menu_item_block', '');
			$this->parse('MENU_'.$child->id, 'tpl_menu_'.$child->id);
		}
	}
	/**
	 * Display login box if user is not logged
	 * @access public
	 * @return void
	 */
	public function setLoginBox()
	{
		if (!Dot_Auth::hasIdentity('user'))
		{
			$this->setFile('tpl_login', 'blocks/login_box.tpl');
			$this->parse('LOGIN_BOX', 'tpl_login');
		}
		else
		{
			$this->setVar('LOGIN_BOX', '');
		}
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
}