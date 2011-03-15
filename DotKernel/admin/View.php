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
	 * Display the specific menu that was declared in configs/menu.xml file
	 * @access public
	 * @return void
	 */
	public function setViewMenu()
	{		
		$dotAuth = Dot_Auth::getInstance();
		if($dotAuth->hasIdentity('admin'))
		{
			$menu_xml = new Zend_Config_Xml(CONFIGURATION_PATH . '/' . $this->route['module'] . '/' . 'menu.xml', 'config');
			$menus = $menu_xml->menu;
			// if we have only one menu, Zend_Config_Xml return a simple array, not an array with key 0(zero)
			if(is_null($menus->{0}))
			{
				$menus = new Zend_Config(array(0=>$menu_xml->menu));						
			}
			$menus = $menus->toArray();

			foreach ($menus as $menu)
			{
				//don't display the menu if display is set to 0, or it doesn't have the ID of 1
				if(0 == $menu['display']) continue;
				if(1 != $menu['id']) continue;

				$this->setFile('tpl_menu', 'blocks/menu.tpl');

				$items = $menu['item'];

				// if we have only one menu, Zend_Config_Xml return a simple array, not an array with key 0(zero)
				if(!isset($items[0]))
				{
					$items = array(0 => $items);
				}

				$this->setBlock('tpl_menu', 'submenu_list', 'submenu_list_block');
				$this->setBlock('tpl_menu', 'menu_list', 'menu_list_block');

				foreach ($items as $menuItem)
				{
					$this->setVar('MENU_TITLE', $menuItem['title']);
					$this->setVar('MENU_LINK', $this->config->website->params->url.'/'.$this->route['module'].'/'.$menuItem['link']);
					$this->setVar('MENU_DESCRIPTION', $menuItem['description']);

					$subItems = $menuItem['subItems']['subItem'];

					foreach ($subItems as $subMenuItem)
					{
						$this->setVar('SUBMENU_TITLE', $subMenuItem['title']);
						$this->setVar('SUBMENU_LINK', $this->config->website->params->url.'/'.$this->route['module'].'/'.$subMenuItem['link']);
						$this->setVar('SUBMENU_DESCRIPTION', $subMenuItem['description']);
						$this->parse('submenu_list_block', 'submenu_list',true);
					}
					$this->parse('menu_list_block', 'menu_list',true);
					$this->parse('submenu_list_block', '');
				}
			}
		}
		$this->parse('MENU', 'tpl_menu');
	}
	/**
	 * Display the info bar only if user is logged id
	 * @access public 
	 * @return void
	 */
	public function setInfoBar()
	{		
		$dotAuth = Dot_Auth::getInstance();
		if($dotAuth->hasIdentity('admin'))
		{			
			$this->setFile('tpl_info', 'blocks/info_bar.tpl');
			$session = Zend_Registry::get('session');
			$this->setVar('ADMIN_USERNAME', $session->admin->username);
			$this->parse('INFO_BAR', 'tpl_info');			
		}
	}
	/**
	 * Create the pagination, based on how many data
	 * @access public
	 * @param array $data
	 * @return string
	 */
	protected function paginator($page)
	{			
		// get route again here, because ajax may have change it
		$route = Zend_Registry::get('route');
		$this->setFile('page_file', 'paginator.tpl');
		$this->setVar('TOTAL_RECORDS', $page->totalItemCount);
		$this->setVar('TOTAL_PAGES', $page->pageCount );
		$this->setBlock('page_file', 'first', 'first_row');
		$this->setBlock('page_file', 'last', 'last_row');
		$this->setBlock('page_file', 'current_page', 'current_row');
		$this->setBlock('page_file', 'other_page', 'other_row');		
		$this->setBlock('page_file', 'pages', 'pages_row');
	
		if(array_key_exists('page',$route))
		{
			unset($route['page']);
		}
		$seo = new Dot_Seo();
		$link = $seo->createCanonicalUrl($route).'page/';
		
		if ($page->current != 1)
		{
			$this->setVar('FIRST_LINK',$link."1");
			$this->parse('first_row', 'first', TRUE);
		}
		else
		{
			$this->parse('first_row', '');
		}
		if ($page->current != $page->last)
		{
			$this->setVar('LAST_LINK',$link.$page->last);
			$this->parse('last_row', 'last', TRUE);
		}
		else
		{
			$this->parse('last_row', '');
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
		$dotAuth = Dot_Auth::getInstance();
		$user = $dotAuth->getIdentity('admin');
		$this->setVar('USERTOKEN', Dot_Kernel::generateUserToken($user->password));
	}
	/**
	 * Display the widget: User Login Piechart
	 * @access private
	 * @param array $widgetOption
	 * @return void
	 */
	private function _displayUserLoginsPiechart($widgetOption)
	{
		// pie chart
		$userModel = new User();
		$userCountry = $userModel->getTopCountryLogins($widgetOption['countCountryUserLogin']);
		$color = $widgetOption['colorCharts']['color'];
		$data = array();
		$i = 0;
		foreach ($userCountry as $name => $country)
		{
			if($country['count'] > 0)
			{				
				$data[] = array('y' => $country['countPercent'], 
								'text' => $name, 
								'color' => $color[$i], 
								'stroke' => $color[$i], 
								'tooltip' => $name.": ".(string)$country['countPercent']."&#37;"
								);
				$i++;
			}
		}
		if(empty($data))
		{
			$data[] = array('y' => 1, //must be a number greater then 0
							'text' => $widgetOption['message']['noUserLogin'], 
							'color' => $color[0], 
							'stroke' => $color[0], 
							'tooltip' => $widgetOption['message']['noUserLogin']
							);
		}
		$this->setVar('PIEDATA', Zend_Json::encode($data));	
	}
	/**
	 * Display widgets content
	 * @access public
	 * @param Zend_Config $value
	 * @return void
	 */
	public function displayWidgets($value)
	{
		// if we have only one widget, Zend_Config_Xml return a simple array, not an array with key 0(zero)
		if(is_null($value->{0}))
		{
			$value = new Zend_Config(array(0=>$value));						
		}
		$widgets = $value->toArray();		
		foreach ($widgets as $key =>$val)
		{
			if($this->varExists($val['token']))
			{
				// initialize the template file where is the widget content
				$this->setFile('tpl_widget', 'blocks/' . strtolower($val['token']) . '.tpl');
				switch ($val['token']) 
				{
					case 'WIDGET_USER_LOGINS':						
						$this->_displayUserLoginsPiechart($val);												
					break;
				}
				// parse the widget content
				$this->parse(strtoupper($val['token']), 'tpl_widget');
			}
		}
		
	}
}