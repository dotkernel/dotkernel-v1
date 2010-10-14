<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
* @copyright  Copyright (c) 2009-2010 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
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
	 * Initialize some parameter
	 * @access public
	 * @return void
	 */
	public function init()
	{
		$this->route = Zend_Registry::get('route');
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
		$this->setVar('IMAGES_URL', $this->config->website->params->url . IMAGES_DIR . '/' .$this->route['module']);
		$this->setVar('IMAGES_SHORT_URL', $this->config->website->params->url . IMAGES_DIR);
		$this->setVar('SITE_URL', $this->config->website->params->url);
		$this->setVar('SKIN', $this->config->settings->admin->skin);
		
		
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
	}
	/**
	 * Display the specific menu that were declared in configs/menu.xml file
	 * @access public
	 * @return void
	 */
	public function setViewMenu()
	{		
		if(Dot_Auth::hasIdentity('admin'))
		{
			$menu_xml = new Zend_Config_Xml(CONFIGURATION_PATH . '/' . $this->route['module'] . '/' . 'menu.xml', 'config');
			$menu = $menu_xml->menu;
			// if we have only one menu, Zend_Config_Xml return a simple array, not an array with key 0(zero)
			if(is_null($menu->{0}))
			{
				$menu = new Zend_Config(array(0=>$menu_xml->menu));						
			}
			$menu = $menu->toArray();
			foreach ($menu as $child)
			{	
				//don't display the menu
				if(0 == $child['display']) continue;		
				$this->setFile('tpl_menu_'.$child['id'], 'blocks/menu_'.$child['type'].'.tpl');
				$this->setBlock('tpl_menu_'.$child['id'], 'top_normal_sub_menu_item', 'top_normal_sub_menu_item_block');
				$this->setBlock('tpl_menu_'.$child['id'], 'top_sub_menu_item', 'top_sub_menu_item_block');
				$this->setBlock('tpl_menu_'.$child['id'], 'top_normal_menu_item', 'top_normal_menu_item_block');
				$this->setBlock('tpl_menu_'.$child['id'], 'top_menu_item', 'top_menu_item_block');
				$this->setBlock('tpl_menu_'.$child['id'], 'top_menu', 'top_menu_block');				
				$tplVariables = array('TOP_MENU_SEL', 
									  'TOP_MENU_LINK', 
									  'TOP_MENU_TARGET', 
									  'TOP_MENU_TITLE', 
									  'TOP_SUB_MENU_LINK', 
									  'TOP_SUB_MENU_TARGET', 
									  'TOP_SUB_MENU_TITLE');				
				$tplBlocks = array('top_menu_block', 
								   'top_menu_item_block', 
								   'top_normal_menu_item_block', 
								   'top_sub_menu_item_block',
								   'top_normal_submenu_item_block');				
				//Initialize all the tag variables and blocks
				$this->initVar($tplVariables,'');			
				$this->initBlock($tplBlocks,'');
				$i = 0;					
				$items = $child['item'];
				// if we have only one menu, Zend_Config_Xml return a simple array, not an array with key 0(zero)
				if(!is_array($items))
				{
					$items = array(0=>$items);						
				}	
				foreach ($items as $key => $val)
				{		
					$this->setVar('TOP_MENU_ID', $i);
					$tplVariables = array('TOP_MENU_SEL', 
					                      'TOP_SUB_MENU_SEL', 
										  'TOP_SUB_MENU_ITEM_SEL');
					$this->initVar($tplVariables,'');
					$menuCondition = (FALSE !== stripos($val['link'], ('' == $this->route['action'])? $this->route['controller'].'/' : $this->route['controller'].'/'.$this->route['action'].'/'));
                    if ($menuCondition)					
					{	//if curent menu is the curent viewed page
						$this->setVar('TOP_MENU_SEL', '_selected');
					}							
					$this->setVar('TOP_MENU_TITLE', $val['title']);
					$this->setVar('TOP_MENU_LINK', $this->config->website->params->url.'/'.$this->route['module'].'/'.$val['link']);
					$this->setVar('TOP_MENU_DESCRIPTION', $val['description']);													
					$this->parse('top_normal_menu_item_block', 'top_normal_menu_item', true);
					if (isset($val['subItems']['subItem']) && count($val['subItems']['subItem']) > 0)
					{	
						$this->parse('top_normal_sub_menu_item_block', '');	
						$subItems = $val['subItems']['subItem'];
						// if we have only one menu, Zend_Config_Xml return a simple array, not an array with key 0(zero)
						if(!is_array($subItems))
						{
							$subItems = array(0=>$subItems);						
						}											
						foreach ($subItems as $k2 => $v2)
						{			
							$this->setVar('TOP_SUB_MENU_SEL', '');
							$this->setVar('TOP_SUB_MENU_TITLE', $v2['title']);
							$this->setVar('TOP_SUB_MENU_LINK', $this->config->website->params->url.'/'.$this->route['module'].'/'.$v2['link']);
							$this->setVar('TOP_SUB_MENU_DESCRIPTION', $v2['description']);
							if (FALSE !== stripos($v2['link'], $this->route['controller'].'/'.$this->route['action'].'/'))
							{	//if curent menu is the curent viewed page
								$this->setVar('TOP_MENU_SEL', '_selected');
								$this->setVar('TOP_SUB_MENU_SEL', '_selected');
							}
							elseif (FALSE !== stripos($v2['link'], $this->route['controller'].'/'))
							{
								$this->setVar('TOP_MENU_SEL', '_selected');								
							}
							$this->parse('top_normal_sub_menu_item_block', 'top_normal_sub_menu_item', true);
						}
						$this->parse('top_sub_menu_item_block', 'top_sub_menu_item', true);	
					}					
					$this->parse('top_menu_item_block', 'top_menu_item', true);
					$this->parse('top_normal_menu_item_block', '');																	
					$i++;
				}					
				$this->parse('top_normal_sub_menu_item_block', 'top_normal_sub_menu_item',true);
				$this->parse('top_menu_block', 'top_menu', true);
				$this->parse('MENU_'.$child['id'], 'tpl_menu_'.$child['id']);
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
		// get route again here, because ajax may have change it
		$route = Zend_Registry::get('route');
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
	
		if(array_key_exists('page',$route))
		{
			unset($route['page']);
		}
		$seo = new Dot_Seo();
		$link = $seo->createCanonicalUrl($route).'page/';
		
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
	/**
	 * Display message - error, warning, info
	 * @access public
	 * @param bool $ajax [optional] - Using ajax, parse only the list content
	 * @return void
	 */
	public function displayMessage($ajax = FALSE)
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
					$this->setVar('MESSAGE_ARRAY', is_string($k) ? $msg = ucfirst($k) . ' - ' . htmlspecialchars($msg) : htmlspecialchars($msg));
					$this->parse('msg_array_row', 'msg_array', true);
				}
			}
			else
			{
				$this->parse('msg_array_row', '');
				$this->setVar('MESSAGE_STRING', htmlspecialchars($session->message['txt']));
			}
			$ajax == TRUE ? 
			$this->parse('AJAX_MESSAGE_BLOCK', 'tpl_msg'):
			$this->parse('MESSAGE_BLOCK', 'tpl_msg');
			unset($session->message);
		}		
	}
	/**
	 * Reset the Template root - from where the tpl files should be taken
	 * @access public
	 * @param string $root
	 * @return void
	 */	
	public function resetRoot($root)
	{
		$this->setRoot($root.'/admin');
	}
	/**
	 * Add the user's token to the template
	 * @access public
	 * @return array
	 */
	public function addUserToken()
	{
		$user=Dot_Auth::getIdentity('admin');
		$this->setVar('USERTOKEN', Dot_Kernel::generateUserToken($user['password']));
	}
}