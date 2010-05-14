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
* Admin View 
* abstract over the Dot_Template class
* @category   DotKernel
* @package    Admin 
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
	 * @static
	 * @param string $root     Template root directory
	 * @param string $unknowns How to handle unknown variables
	 * @param array  $fallback Fallback paths
	 * @return Dot_Template
	 */
	public static function getInstance($root = '.', $unknowns = 'remove', $fallback='')
	{
		if (null === self::$_instance) {
			self::$_instance = new self($root, $unknowns, $fallback);
			self::$_instance->settings = Zend_Registry::get('settings');
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
	 * Set the title html tag
	 * @access public
	 * @param Dot_Settings $settings
	 * @param string $pageTitle
	 * @return void
	 */
	public function setViewTitle($settings, $pageTitle)
	{
		$this->setVar('PAGE_TITLE', $pageTitle . ' | ' .$settings->defaultPageTitle);
		$this->setVar('PAGE_CONTENT_TITLE', $pageTitle );
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
		if(Dot_Auth::hasIdentity('admin'))
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
				$this->setBlock('tpl_menu_'.$child->id, 'top_sub_menu_item', 'top_sub_menu_item_block');
				$this->setBlock('tpl_menu_'.$child->id, 'top_normal_menu_item', 'top_normal_menu_item_block');
				$this->setBlock('tpl_menu_'.$child->id, 'top_menu_item', 'top_menu_item_block');
				$this->setBlock('tpl_menu_'.$child->id, 'top_menu', 'top_menu_block');				
				$tplVariables = array('TOP_MENU_SEL', 
									  'TOP_MENU_LINK', 
									  'TOP_MENU_TARGET', 
									  'TOP_MENU_TITLE', 
									  'TOP_SUB_MENU_LINK', 
									  'TOP_SUB_MENU_TARGET', 
									  'TOP_SUB_MENU_TITLE');				
				$tplBlocks = array('top_menu_item_block', 
								   'top_menu_block', 
								   'top_sub_menu_item_block', 
								   'top_normal_menu_item_block');				
				//Initialize all the tag variables and blocks
				$this->initVar($tplVariables,'');			
				$this->initBlock($tplBlocks,'');
				
				$i = 0;					
				$items = $child->item;
				// if we have only one menu, Zend_Config_Xml return a simple array, not an array with key 0(zero)
				if(is_null($items->{0}))
				{
					$items = new Zend_Config(array(0=>$child->item));						
				}			
				foreach ($items as $key => $val)
				{		
					// display menus based on user is logged in or not
					$this->setVar('TOP_MENU_ID', $i);
					$tplVariables = array('TOP_MENU_SEL', 
					                      'TOP_SUB_MENU_SEL', 
										  'TOP_SUB_MENU_ITEM_SEL');
					$this->initVar($tplVariables,'');	
					if (FALSE !== stripos($val->link, $this->requestController.'/'.$this->requestAction.'/'))
					{	//if curent menu is the curent viewed page
						$this->setVar('TOP_MENU_SEL', '_selected');
						$this->setVar('TOP_SUB_MENU_SEL', '_selected');
					}			
					$this->setVar('TOP_MENU_TITLE', $val->title);
					$this->setVar('TOP_MENU_LINK', $config->website->params->url.'/'.$this->requestModule.'/'.$val->link);
					$this->setVar('TOP_MENU_DESCRIPTION', $val->description);													
					$this->parse('top_normal_menu_item_block', 'top_normal_menu_item', true);
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
							$this->setVar('TOP_SUB_MENU_TITLE', $v2->title);
							$this->setVar('TOP_SUB_MENU_LINK', $config->website->params->url.'/'.$this->requestModule.'/'.$v2->link);
							$this->setVar('TOP_SUB_MENU_DESCRIPTION', $v2->description);
							if (FALSE !== stripos($val->link, $this->requestController.'/'))
							{	//if curent menu is the curent viewed page
								$this->parse('top_sub_menu_item_block', 'top_sub_menu_item', true);	
							}							
						}
					}
					$this->parse('top_menu_item_block', 'top_menu_item', true);
					$this->parse('top_normal_menu_item_block', '');
																	
					$i++;
				}					
				$this->parse('top_menu_block', 'top_menu', true);
				$this->parse('MENU_'.$child->id, 'tpl_menu_'.$child->id);
			}
		}
	}
	/**
	 * Display the info bar only if user is logged id
	 * @access public 
	 * @return void
	 */
	public function setInfoBar()
	{		
		if(Dot_Auth::hasIdentity('admin'))
		{			
			$this->setFile('tpl_info', 'blocks/info_bar.tpl');
			$session = Zend_Registry::get('session');
			$this->setVar('ADMIN_USERNAME', $session->admin['username']);
			$this->parse('INFO_BAR', 'tpl_info');			
		}
	}
	/**
	 * Create the pagination, based on how many data
	 * @access public
	 * @param array $data
	 * @param int $current_page [optional]
	 * @return string
	 */
	protected function paginator($adapter, $currentPage = 1)
	{		
		$paginator = new Zend_Paginator($adapter);
		$paginator->setItemCountPerPage($this->settings->resultsPerPage);
		$paginator->setCurrentPageNumber($currentPage);
		$paginator->totalItems = $adapter->count();
		$page = $paginator->getPages();	
		$this->setFile('page_file', 'paginator.tpl');
		$this->setVar('TOTAL_RECORDS', $paginator->totalItems);
		$this->setVar('TOTAL_PAGES', $page->pageCount );
		$this->setBlock('page_file', 'previous', 'previous_row');
		$this->setBlock('page_file', 'next', 'next_row');
		$this->setBlock('page_file', 'current_page', 'current_row');
		$this->setBlock('page_file', 'other_page', 'other_row');		
		$this->setBlock('page_file', 'pages', 'pages_row');

		$param = Zend_Registry::get('param');
		$link = (array_key_exists('page',$param)) ?  '' : 'page/';
		
		if ($page->first != $page->current)
		{
			$this->setVar('PREVIOUS_LINK',$link.($page->current-1));
			$this->parse('previous_row', 'previous', TRUE);
		}
		else
		{
			$this->parse('previous_row', '');
		}
		if ($page->last>0 && $page->last != $page->current)
		{
			$this->setVar('NEXT_LINK',$link.$page->next);
			$this->parse('next_row', 'next', TRUE);
		}
		else
		{
			$this->parse('next_row', '');
		}
		foreach ($page->pagesInRange as $val)
		{
			$this->setVar('PAGE_NUMBER', $val);
			$this->parse('other_row','');
			$this->parse('current_row','');
			if($val == $page->current)
			{
				$this->parse('current_row','current_page', TRUE);
				//$this->parse('other_row','');
			}
			else
			{
				$this->setVar('PAGE_LINK', $link.$val);
				//$this->parse('current_row','');
				$this->parse('other_row','other_page', TRUE);
			}				
			$this->parse('pages_row', 'pages', TRUE);
		}
		$this->parse('PAGINATION', 'page_file');				
	}
}