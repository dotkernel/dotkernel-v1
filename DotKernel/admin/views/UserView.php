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
		$this->settings = Zend_Registry::get('settings');
		$this->config = Zend_Registry::get('configuration');
		$this->route = Zend_Registry::get('route');
	}
	/**
	 * List users
	 * @access public
	 * @param string $templateFile
	 * @param array $list
	 * @param int $page
	 * @param bool $ajax - Using ajax, parse only the list content
	 * @return void
	 */
	public function listUser($templateFile, $list, $page, $ajax = false)
	{
		$this->tpl->setFile('tpl_main', 'user/' . $templateFile . '.tpl');
		$this->tpl->setBlock('tpl_main', 'list', 'list_block');
		$this->tpl->paginator($list['paginatorAdapter'],$page);
		$this->tpl->addUserToken();
		$this->tpl->setVar('PAGE', $page);
		$this->tpl->setVar('ACTIVE_URL', '/admin/user/activate/');
		foreach ($list['data'] as $k => $v)
		{
		    $this->tpl->setVar('BG', $k%2+1);
			$this->tpl->setVar('ID', $v['id']);
			$this->tpl->setVar('USERNAME', $v['username']);
			$this->tpl->setVar('EMAIL', $v['email']);
			$this->tpl->setVar('FIRSTNAME', $v['firstName']);
			$this->tpl->setVar('LASTNAME', $v['lastName']);
			$this->tpl->setVar('DATE_CREATED', Dot_Kernel::timeFormat($v['dateCreated'], 'long'));
			$this->tpl->setVar('ISACTIVE', $v['isActive']*(-1)+1);
			$this->tpl->setVar('ACTIVE_IMG', $v['isActive'] == 1 ? 'active' : 'inactive');
			$this->tpl->parse('list_block', 'list', true);
		}
		if($ajax)
		{
			$this->tpl->pparse('AJAX', 'tpl_main');
			exit;
		}
	}
	/**
	 * Display user details. It is used for add and update actions
	 * @access public
	 * @param string $templateFile
	 * @param array $data [optional]
	 * @return void
	 */
	public function details($templateFile, $data=array())
	{
		$this->tpl->setFile('tpl_main', 'user/' . $templateFile . '.tpl');
		$this->tpl->setVar('ACTIVE_1', 'checked');
		$this->tpl->addUserToken();
		foreach ($data as $k=>$v)
		{
		    $this->tpl->setVar(strtoupper($k), $v);			
			if('isActive' == $k)
			{
				$this->tpl->setVar('ACTIVE_'.$v, 'checked');
				$this->tpl->setVar('ACTIVE_'.$v*(-1)+1, '');
			}
		}
	}
	/**
	 * Display user logins list
	 * @access public
	 * @param string $templateFile
	 * @param array $list 
	 * @param int $page
	 * @return void
	 */
	public function loginsUser($templateFile, $list, $page, $browser, $loginDate, $sortField, $orderBy)
	{
		$dotGeoip = new Dot_Geoip();
		$this->tpl->setFile('tpl_main', 'user/' . $templateFile . '.tpl');
		$this->tpl->setBlock('tpl_main', 'browser', 'browser_row');
		$xml = new Zend_Config_Xml(CONFIGURATION_PATH.'/browser.xml');
		$browserArray = $xml->name->type->toArray();
		foreach ($browserArray as $key => $val)
		{
			$this->tpl->setVar('BROWSERNAME', ucfirst($val['uaBrowser']));
			ucfirst($val['uaBrowser']) == $browser ?
				$this->tpl->setVar('BROWSERSEL', 'selected'):
				$this->tpl->setVar('BROWSERSEL', '');
			$this->tpl->parse('browser_row', 'browser', true);
			
		}
		$this->tpl->setVar('FILTERDATE', $loginDate);				
		$this->tpl->setBlock('tpl_main', 'list', 'list_block');
		$this->tpl->paginator($list['paginatorAdapter'],$page);
		$this->tpl->setVar('PAGE', $page);
		$this->tpl->setVar('FILTER_URL', '/admin/user/logins');

		$sortableFields = array('username', 'dateLogin');
		foreach ($sortableFields as $field)
		{
			$linkSort = $this->config->website->params->url. '/' . $this->route['module'] . '/' . $this->route['controller']. '/'.$this->route['action'].'/sort/'.$field.'/order/';
			$linkSort .= ($orderBy == 'asc') ? 'desc' : 'asc';  
			$this->tpl->setVar('LINK_SORT_'.strtoupper($field), $linkSort);
			if($field != $sortField)
			{
				$sortClass = 'sortable';
			}
			elseif($orderBy == 'asc')
			{
				$sortClass = 'sort_up';
			}
			else
			{
				$sortClass = 'sort_down';
			}
			$this->tpl->setVar('CLASS_SORT_'.strtoupper($field), $sortClass);			
		}
		
		foreach ($list['data'] as $k => $v)
		{
			$country = $dotGeoip->getCountryByIp($v['ip']);
		  $this->tpl->setVar('BG', $k%2+1);
			$this->tpl->setVar('ID', $v['id']);
			$this->tpl->setVar('USERID', $v['userId']);
			$this->tpl->setVar('USERNAME', $v['username']);
			$this->tpl->setVar('IP', $v['ip']);
			$this->tpl->setVar('COUNTRYIMAGE', strtolower($country[0]));
			$this->tpl->setVar('COUNTRYNAME', $country[1]);
			$this->tpl->setVar('REFERER', $v['referer']);
			$this->tpl->setVar('WHOISURL', $this->settings->whoisUrl);
			$this->tpl->setVar('USERAGENT', $v['userAgent']);
			$this->tpl->setVar('BROWSERIMAGE', Dot_Kernel::getBrowserIcon($v['userAgent']));
			$os = Dot_Kernel::getOsIcon($v['userAgent']);			
			$this->tpl->setVar('OSIMAGE', $os['icon']);
			$this->tpl->setVar('OSMAJOR', $os['major']);
			$this->tpl->setVar('OSMINOR', $os['minor']);
			$this->tpl->setVar('DATELOGIN', Dot_Kernel::timeFormat($v['dateLogin'], 'long'));
			$this->tpl->parse('list_block', 'list', true);
		}	
	}
}